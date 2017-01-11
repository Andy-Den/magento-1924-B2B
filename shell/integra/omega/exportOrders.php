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
		'domain' => '@4vets.com.br',
	),
	'website_id' => $websiteId, //NULL ou o id do website
	'stores_id' => NULL, //NULL ou array com as stores. Irá ignorar o website ID
	'especial_lines' => array(
		array (
			array('type' => 'dynamic', 'function' => 'getOrderId', 'params' => array()),//, 'maxsize' => 6), //Codigo do vendedor
			array('type' => 'dynamic', 'function' => 'getSalesRep', 'params' => array()),//, 'maxsize' => 6), //Codigo do vendedor
			array('type' => 'dynamic', 'function' => 'getCustomerIdErp', 'params' => array()), //Codigo do Cliente
			array('type' => 'dynamic', 'function' => 'getPaymentMethod', 'params' => array()), //ID ERP da forma de pagamento. Boleto, cartão, etc.
			array('type' => 'dynamic', 'function' => 'getPaymentCondition', 'params' => array()),//ID ERP da condição de pagamento. A vista com 3%, etc
			array('type' => 'dynamic', 'function' => 'getPaymentId', 'params' => array()), //Numero do pedido no sistema de pedido ou site
			array('type' => 'dynamic', 'function' => 'getOrderDate', 'params' => array('format' => 'yyyy-MM-dd')), //Data do pedido formato: yyyy-MM-dd
			array('type' => 'dynamic', 'function' => 'getOrderUpdateDate', 'params' => array('format' => 'yyyy-MM-dd')), //Data do pedido formato: yyyy-MM-dd
			array('type' => 'dynamic', 'function' => 'getOrderStatus', 'params' => array()), //Data do pedido formato: ddmmyyyy
			array('type' => 'dynamic', 'function' => 'getOrderIdTabela', 'params' => array()),
			array('type' => 'dynamic', 'function' => 'getCustomerNote', 'params' => array()),
		)
	),
	'items' => array(
		array('type' => 'dynamic', 'function' => 'getProductQty', 'params' => array()),
		array('type' => 'dynamic', 'function' => 'getProductIdErp', 'params' => array()),
		array('type' => 'dynamic', 'function' => 'getProductDiscountPercent', 'params' => array('decimal' => true, 'remove_payment_discount' => true)),
		array('type' => 'dynamic', 'function' => 'getProductPrice', 'params' => array()),
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
	//->addAttributeToFilter('entity_id', array('in' => array(773)));
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
	try
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

	//Flag para saber se o pedido foi exportado corretamente.
	$exportedOrder = false;

	/**
	 * Dividir o pedido por tabela
	 */

	$tableprice = getAllOrderTableprice($order);

	//Não deixo exportar pedidos caso todos os produtos não tiverem representates setados.
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

	debug('Exportando ordem de nr. ' . $order->getId() . ' | ' . getCustomerEmail($order));

	//Setar a store da ordem para pegar os atributos corretos
	Mage::app()->setCurrentStore($order->getStoreId());

	//Dividir o pedido por representante
	foreach ($salesrep as $rep) {

		//Carregar o representante
		$rep = Mage::getModel('fvets_salesrep/salesrep')->load($rep['salesrep_id']);

		$salesRepId = getSalesRep($order);
		$orderId = $order->getId();

		//Dividir o pedido por tabela
		foreach ($tableprice as $table) {

			$table = $table['tableprice'];

			foreach ($config['especial_lines'] as $line) {
				$value = '';
				foreach ($line as $col)
				{
					if ($col['function'] == 'getSalesRep') //Usar o representante já encontrado
					{
						$value .= $rep->getIdErp() . '|';
					}
					elseif ($col['function'] == 'getOrderIdTabela') //Usar a tabela já encontrada
					{
						$value .= $table . '|';
					}
					else
					{
						$value .= getColValue($order, $col);
					}

					//Adiciona o ID do representante na order para que possa ser dividida.
					if ($col['function'] == 'getOrderId') {
						$value = substr($value, 0, strlen($value - 1)) . '-' . $rep->getId() . '-' . preg_replace("([^\w\d\_])", '_', $table) . '|';
					}
				}
				number_format(Mage::helper('checkout')->getQuote()->getGrandTotal(), 2);
				$out .= rtrim($value, $config['file_delimiter']) . "\r\n";
			}

			$countItems = 0;
			foreach ($order->getAllVisibleItems() as $_item) {

				//Se o produto não for desse representante, continua para o próximo item.
				if ($_item->getSalesrepId() != $rep->getId()) {
					continue;
				}

				//Se o produto não for dessa tabela, continua para o próximo item.
				if ($_item->getTableprice() != $table) {
					continue;
				}

				// remove os produtos com valor 0,00
				if ($_item->getPrice() == 0.0000) {
					//continue;
				}
				foreach ($config['items'] as $col) {
					$col['params']['item'] = $_item;
					$out .= getColValue($order, $col);
				}
				$out = rtrim($out, $config['file_delimiter']) . "\r\n";

				$countItems++;
			}

			//Se a quantidade de produtos exportados for 0, não continua.
			if ($countItems <= 0)
			{
				$out = '';
				continue;
			}

			//define o nome do arquivo
			$fileName = $config['file_prefix'] . $orderId . '-' . $rep->getId() . '-' . preg_replace("([^\w\d\_])", '_', $table);

			//salva o arquivo numa pasta local
			$remoteFile = $fileName . $config['file_extension'];

			if ($local == true):$file = "$testDirectoryExport/orders/$fileName" . $config['file_extension'];
			else : $file = "$directoryExport/orders/$fileName" . $config['file_extension']; endif;

			$current = file_get_contents($file);
			if (file_put_contents($file, $out)) {
				$exportedOrder = true;
			} else {
				$exportedOrder = false;
				debug('Erro ao salvar pedido nr. ' . $order->getId() . ' usuário: ' . getCustomerEmail($order));
			}

			debug($out);

			$out = '';
		}

	}  //End foreach por representante

	if ($exportedOrder)
	{
		try {
			$order->setExported(1);
			//$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, Mage_Sales_Model_Order::STATE_PROCESSING, 'Pedido enviado para a distribuidora.', true);
			$order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
			$history = $order->addStatusHistoryComment('Pedido enviado para a distribuidora.');
			$history->setIsCustomerNotified(true);

			//Enviar email para representante caso não tenha sido enviado
			if (!$order->getSalesrepEmail())
			{
				Mage::helper('fvets_salesrep')->sendRepComissionEmail(NULL, $order);
			}

			$order->save();
		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);
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
	} catch(Exception $e)
	{
		debug('Erro: ' . $e->getMessage());
	}
}; //End foreach por pedidos

sendDebug('Omega');