<?php
require_once './configIntegra.php';
require_once '../_functions/exportOrders.php';

//Configuration array
$config = array(
	'one_file_per_order' => true,
	'file_delimiter' => ';',
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
			array('type' => 'default', 'value' => '1', 'maxsize' => 1), //Tipo de registro. Texto fixo contendo: 1
			array('type' => 'default', 'value' =>  date('d/m/Y'), 'maxsize' => 10), //Data da atualização da base de dados: dd/mm/yyyy
			array('type' => 'default', 'value' =>  'SITE', 'maxsize' => 20), //Versao do sistema de pedidos: Texto fixo contendo: SITE
		),
		array (
			array('type' => 'default', 'value' => 'C', 'maxsize' => 1), //Tipo de Registro. Texto fixo: C
			array('type' => 'dynamic', 'function' => 'getSalesRep', 'params' => array(), 'maxsize' => 6), //Codigo do vendedor
			array('type' => 'dynamic', 'function' => 'getIncrementId', 'params' => array(), 'maxsize' => 20), //Numero do pedido no sistema de pedido ou site
			array('type' => 'dynamic', 'function' => 'getCustomerIdErp', 'params' => array(), 'maxsize' => 10), //Codigo do Cliente
			array('type' => 'default', 'value' => '1', 'maxsize' => 4), //Codigo da forma de pagamento
			array('type' => 'dynamic', 'function' => 'getOrderDate', 'params' => array('format' => 'ddMMyyyy'), 'maxsize' => 8), //Data do pedido formato: ddmmyyyy
			array('type' => 'dynamic', 'function' => 'getOrderItensQty', 'params' => array(), 'maxsize' => 3), //Quantidade de itens do pedido
			array('type' => 'default', 'value' => '', 'maxsize' => 1), //Não Utilizado
			array('type' => 'default', 'value' => '0', 'maxsize' => 1), //Pedido Broker:0 - não, 1	- sim
			array('type' => 'default', 'value' => 'D', 'maxsize' => 1), //Modo de Pagamento:
			array('type' => 'dynamic', 'function' => 'getCustomerNote', 'params' => array(), 'maxsize' => 254), //Observações do pedido
			//array('type' => 'dynamic', 'function' => 'getCustomerCpf', 'params' => array('onlynumber' => true), 'maxsize' => 11), //Codigo do Cliente
			//array('type' => 'dynamic', 'function' => 'getCustomerName', 'params' => array(), 'maxsize' => 100), //Codigo do Cliente
			//array('type' => 'dynamic', 'function' => 'getCustomerStreet', 'params' => array('line' => 0), 'maxsize' => 100), //Logradouro do cliente
			//array('type' => 'dynamic', 'function' => 'getCustomerStreet', 'params' => array('line' => 1), 'maxsize' => 20), //Numero do logradouro
			//array('type' => 'dynamic', 'function' => 'getCustomerStreet', 'params' => array('line' => 2), 'maxsize' => 50), //Complemento do cliente
			//array('type' => 'dynamic', 'function' => 'getCustomerDistrict', 'params' => array(), 'maxsize' => 8), //Bairro do cliente
			//array('type' => 'dynamic', 'function' => 'getCustomerCep', 'params' => array(), 'maxsize' => 8), //Cep do cliente
			//array('type' => 'dynamic', 'function' => 'getCustomerPhones', 'params' => array(), 'maxsize' => 100), //Telefones
			//array('type' => 'dynamic', 'function' => 'getCustomerCity', 'params' => array(), 'maxsize' => 100), //Telefones
			//array('type' => 'dynamic', 'function' => 'getCustomerRegion', 'params' => array(), 'maxsize' => 2), //Telefones
			//array('type' => 'default', 'value' => '1', 'maxsize' => 1), //Tipo de Negocio do cliente 1 - Revenda
		)
	),
	'items' => array(
		array('type' => 'default', 'value' => 'I', 'maxsize' => 1),
		array('type' => 'dynamic', 'function' => 'getProductIdErp', 'params' => array(), 'maxsize' => 6),
		array('type' => 'dynamic', 'function' => 'getProductQty', 'params' => array(), 'maxsize' => 4),
		array('type' => 'dynamic', 'function' => 'getProductDiscountPercent', 'params' => array(), 'maxsize' => 5),
		array('type' => 'dynamic', 'function' => 'getProductPrice', 'params' => array(), 'maxsize' => 15),
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
	//->addAttributeToFilter('store_id', 2)
	//->addAttributeToFilter('entity_id', array('in' => array(433,434,435,430,431)));
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

$out = '';
$salesRepId = '';
$orderId = '';

debug($collection->count() . ' Pedidos para serem exportados!');

foreach ($collection as $order)
{
	try
	{
		//Verifica se o usuario se encontra entre os que nao devem ser exportados
		$ignore = false;
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
			debug('Não exportado ordem nr. ' . $order->getId() . '('.$order->getState().')' . ' usuário: ' . getCustomerEmail($order));
			$order->setExported(2);
			$order->getResource()->saveAttribute($order, 'exported');
			continue;
		}

		//Setar a store da ordem para pegar os atributos corretos
		Mage::app()->setCurrentStore($order->getStoreId());

		debug('Exportando ordem de nr. ' . $order->getId() . ' | ' . getCustomerEmail($order));
		$salesRepId = getSalesRep($order);
		$orderId = $order->getId();


		/**
		 * Verificar se todos os itens têm representante vinculados
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

		foreach ($config['especial_lines'] as $line)
		{
			$value = '';
			foreach ($line as $col)
			{
				$value .= getColValue($order, $col);
			}
		number_format(Mage::helper('checkout')->getQuote()->getGrandTotal(), 2);
			$out .= $value . "\r\n";
		}

		foreach ($order->getAllVisibleItems() as $_item)
		{
			// remove os produtos com valor 0,00
			if ($_item->getPrice() == 0.0000) {
				continue;
			}
			foreach ($config['items'] as $col)
			{
				$col['params']['item'] = $_item;
				$out .= getColValue($order, $col);
			}
			$out .= "\r\n";
		}

		//define o nome do arquivo
		$fileName = 	'PV' . substr(str_pad('1300', 6, "0", STR_PAD_LEFT), 0, 6) . str_pad($orderId, 9, "0", STR_PAD_LEFT);

		//salva o arquivo numa pasta local
		$remoteFile = $fileName . '.MSS';

		$file = "$directoryExport/orders/$fileName.MSS";
		//$file =  "$testDirectoryExporter/orders/$fileName.MSS";
		$current = file_get_contents($file);
		if (file_put_contents($file, $out))
		{
			$order->setExported(1);
			$order->setStatus('distribuidora');
			$order->save();

			//Enviar email para representante caso não tenha sido enviado
			if (!$order->getSalesrepEmail())
			{
				Mage::helper('fvets_salesrep')->sendRepComissionEmail(null, $order);
			}
		}
		else
		{
			debug('Diretório não encontrado: ' . $file);
		}

		debug($out);

		$out = '';


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
};

//echo $out;


/*foreach($executionTime as $function => $time)
{
	echo $function . ' | ' . ($time['time'] / $time['count']) . "\n";
}*/

sendDebug('Peclam');