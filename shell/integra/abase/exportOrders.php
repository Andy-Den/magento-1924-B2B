<?php
require_once './configIntegra.php';
require_once '../_functions/exportOrders.php';

//Configuration array
$config = array(
	'one_file_per_order' => true,
	'file_delimiter' => '|',
	'file_prefix' => '',
	'file_extension' => '.csv',
	'remove_file_delimiter' => true,
	'ignore_customers' => array(
		//'email' => 'ti+loja@4vets.com.br',
		//'email' => 'ti+representante@4vets.com.br',
        'domain' => '@4vets.com.br',
	),
	'website_id' => $websiteId, //NULL ou o id do website
	'stores_id' => NULL, //NULL ou array com as stores. Irá ignorar o website ID
	'especial_lines' => array(
		array (
			array('type' => 'dynamic', 'function' => 'getOrderId', 'params' => array()),//, 'maxsize' => 6), //Codigo do vendedor
			array('type' => 'dynamic', 'function' => 'getOrderIdErp', 'params' => array()),//, 'maxsize' => 6), //Codigo do vendedor
			//array('type' => 'dynamic', 'function' => 'getSalesRep', 'params' => array()),//, 'maxsize' => 6), //Codigo do vendedor
			array('type' => 'dynamic', 'function' => 'getCustomerIdErp', 'params' => array()), //Codigo do Cliente
			array('type' => 'dynamic', 'function' => 'getPaymentMethod', 'params' => array()), //ID ERP da forma de pagamento. Boleto, cartão, etc.
			array('type' => 'dynamic', 'function' => 'getPaymentCondition', 'params' => array()),//ID ERP da condição de pagamento. A vista com 3%, etc
			array('type' => 'dynamic', 'function' => 'getOrderDate', 'params' => array('format' => 'yyyy-MM-dd')), //Data do pedido formato: ddmmyyyy
			array('type' => 'dynamic', 'function' => 'getOrderUpdateDate', 'params' => array('format' => 'yyyy-MM-dd')), //Data do pedido formato: ddmmyyyy
			array('type' => 'dynamic', 'function' => 'getOrderStatus', 'params' => array()), //Status do pedido
			array('type' => 'dynamic', 'function' => 'getOrderIdTabela', 'params' => array()), //Id tabela do pedido
			array('type' => 'dynamic', 'function' => 'getCustomerNote', 'params' => array()),//Coisas que os usuários escreveram
		)
	),
	'items' => array(
		array('type' => 'dynamic', 'function' => 'getProductIdSalesrep', 'params' => array()),
		array('type' => 'dynamic', 'function' => 'abaseGetProductTypeBrinde', 'params' => array()),
		array('type' => 'dynamic', 'function' => 'getProductQty', 'params' => array()),
		array('type' => 'dynamic', 'function' => 'getProductIdErp', 'params' => array()),
		array('type' => 'dynamic', 'function' => 'getProductDiscountPercent', 'params' => array()),
		array('type' => 'dynamic', 'function' => 'getProductPrice', 'params' => array()),
		array('type' => 'dynamic', 'function' => 'getProductFinalPrice', 'params' => array()),
		array('type' => 'dynamic', 'function' => 'abaseGetProductTypeCredit', 'params' => array()),
	)
);

// Set an Admin Session
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
Mage::getSingleton('core/session', array('name' => 'adminhtml'));
$userModel = Mage::getModel('admin/user');
$userModel->setUserId(1);
$session = Mage::getSingleton('admin/session');
$session->setUser($userModel);
$session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());

$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

/* Get orders collection of pending orders, run a query */
$collection = Mage::getModel('sales/order')
	->getCollection()
	->addAttributeToSelect('*')
    //->addAttributeToFilter('entity_id', array('in' => array(1573)));
    ->addFieldToFilter('exported', array('null' => true))
;

//Se houver uma ou mais lojas setadas no config, pega os pedidos dessas lojas.
//Senão pega o website setado
if (isset($config['stores_id']))
{
	if (is_array($config['stores_id']) && count($config['stores_id']) > 0)
	{
		$collection->addAttributeToFilter('store_id', array('in' => $config['stores_id']));
	}
	else if (!is_array($config['stores_id']))
	{
		$collection->addAttributeToFilter('store_id', $config['stores_id']);
	}
}
else if (isset($config['website_id']))
{
	$website = Mage::getModel('core/website')->load($config['website_id']);
	foreach ($website->getGroups() as $group) {
		$stores = $group->getStores();
		foreach ($stores as $store) {
			$config['stores_id'][] = $store->getId();
		}
	}

	$collection->addAttributeToFilter('store_id', array('in' => $config['stores_id']));
}

//Exporta as ordens
$out = '';
$salesRepId = '';
$orderId = '';

debug($collection->count() . ' Pedidos para serem exportados!');

foreach ($collection as $order)
{
	$ignore = false;
	//Verifica se o usuario se encontra entre os que nao devem ser exportados
	foreach ($config['ignore_customers'] as $key => $value)
	{
		if ($key == 'email')
		{
			if (getCustomerEmail($order) == $value)
			{
				$ignore = true;
				continue;
			}
		}
		if ($key == 'domain')
		{
			if (strpos(getCustomerEmail($order), $value))
			{
				$ignore = true;
				continue;
			}
		}
	}

	if ($ignore || $order->getState() == Mage_Sales_Model_Order::STATE_CANCELED)
	{
		debug('Não exportado ordem nr. ' . $order->getId() . ' usuário: ' . getCustomerEmail($order));
		$order->setExported(2);
		$order->getResource()->saveAttribute($order, 'exported');
		continue;
	}

	//Setar a store da ordem para pegar os atributos corretos
	Mage::app()->setCurrentStore($order->getStoreId());

	debug('Exportando ordem de nr. ' . $order->getId() . ' | ' . getCustomerEmail($order));
	$salesRepId = getSalesRep($order);
	$orderId = $order->getId();

	//Flag para saber se o pedido foi exportado corretamente.
	$exportedOrder = false;


	/**
	 * Dividir o pedido por tabela
	 */

	$tableprice = getAllOrderTableprice($order);

	//Não deixo exportar pedidos caso todos os produtos não tiverem tabelas setadas.
	foreach ($tableprice as $table) {
		if ($table['tableprice'] == NULL) {
			debug('Não exportado ordem nr. ' . $order->getId() . ' usuário: ' . getCustomerEmail($order) . ' tabela de preço incorreta.');
			sendEmailForLackOfTableprice($order);
			continue 2;
		}

	}

	/**
	 * Dividir o pedido por representante
	 */

	$salesrep = getAllOrderSalesrep($order);

	//Não deixo exportar pedidos caso todos os produtos não tiverem representates setados.
	foreach ($salesrep as $rep) {
		if ($rep['salesrep_id'] == NULL) {
			debug('Não exportado ordem nr. ' . $order->getId() . ' usuário: ' . getCustomerEmail($order) . ' Id de representante incorreto.');
			sendEmailForLackOfSalesrep($order);
			continue 2;
		}

	}

	foreach ($salesrep as $rep)
	{

		$rep = Mage::getModel('fvets_salesrep/salesrep')->load($rep['salesrep_id']);

		foreach ($config['especial_lines'] as $line)
		{
			$value = '';
			foreach ($line as $col)
			{
				if ($col['function'] == 'getSalesRep') //Usar o representante já encontrado
				{
					$value .= $rep->getIdErp() . '|';
				}
				else
				{
					try
					{
						$value .= getColValue($order, $col);
					}
					catch (Exception $e)
					{
						debug('Erro: ' . $e->getMessage());
						continue 3;
					}
				}

				//Adiciona o ID do representante na order para que possa ser dividida.
				if ($col['function'] == 'getOrderId')
				{
					$value = substr($value, 0, strlen($value - 1)) . '-' . $rep->getId() . '|';
				}
			}

			number_format(Mage::helper('checkout')->getQuote()->getGrandTotal(), 2);
			$out .= substr($value, 0, strlen($value) - 1) . "\r\n";

		}

		foreach ($order->getAllVisibleItems() as $_item)
		{
			//Se o produto não for desse representante, continua para o próximo item.
			if ($_item->getSalesrepId() != $rep->getId())
			{
				continue;
			}

			// remove os produtos com valor 0,00
			//if ($_item->getPrice() == 0.0000) {
			//	continue;
			//}
			foreach ($config['items'] as $col)
			{
				$col['params']['item'] = $_item;
				$out .= getColValue($order, $col);
			}
			$out = rtrim($out,$config['file_delimiter']) . "\r\n";
		}

		//define o nome do arquivo
		$fileName = 	$config['file_prefix'] . $orderId . '-' . $rep->getId();

		//salva o arquivo numa pasta local
		$remoteFile = $fileName . $config['file_extension'];

		if ($local == true ):$file = "$testDirectoryExport/orders/$fileName".$config['file_extension'];
		else : $file =  "$directoryExport/orders/$fileName".$config['file_extension']; endif;

		$current = file_get_contents($file);


		if (file_put_contents($file, $out))
		{
			$exportedOrder = true;
		}
		else
		{
			$exportedOrder = false;
			debug('Erro ao salvar pedido nr. ' . $order->getId() . ' usuário: ' . getCustomerEmail($order));
		}

		debug($out);

		$out = '';

	} //End foreach por representante

	if ($exportedOrder)
	{
		try {
			$order->setExported(1);
			//$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Mage_Sales_Model_Order::STATE_PROCESSING, 'Pedido enviado para a distribuidora.', true);
			$order->setStatus('distribuidora');
			$history = $order->addStatusHistoryComment('Pedido enviado para a distribuidora.');
			$history->setIsCustomerNotified(true);

			//Enviar email para representante caso não tenha sido enviado
			if (!$order->getSalesrepEmail())
			{
				Mage::helper('fvets_salesrep')->sendRepComissionEmail(null, $order);
			}

			$order->save();
		}catch (Mage_Core_Exception $e) {
			//Mage::logException($e);
			debug('Erro: ' . $e->getMessage());
			//mage::throwException(Mage::helper('core')->__('Nao foi possivel exportar o pedido: %s', $e->getMessage()));
		}
	}

	/*$resultSendFile = sendFileByFtp($file, $remoteFile);

	//se o arquivo foi enviado com sucesso
	if ($resultSendFile) {
		//...
		//seta a ordem como enviada;
		//$order->setExported(1);
		//$order->save();
		echo ' - - - - - - - - - - - - - - - - - - -' . "\r\n";
	}*/
} //End foreach por pedidos

sendDebug('Abase');

function abaseGetProductTypeBrinde($order, $param = array())
{
	if ($param['item']->getPrice() > 0)
	{
		return '0';
	}
	else
	{
		return '1';
	}
}

function abaseGetProductTypeCredit($order, $param = array())
{
	return $param['item']->getUsesCredits();
}
