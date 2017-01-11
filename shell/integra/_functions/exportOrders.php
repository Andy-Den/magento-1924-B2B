<?php

$debugMessage = '';

function debug($message)
{
	global $debug, $debugMessage;

	if ($debug)
		echo $message . "\n";

	$debugMessage .= $message . "\n";
}

function sendDebug($website)
{
	global $debugMessage, $local;

	if (!$local)
	{
		Mage::helper('datavalidate')->createChannel('export_orders');
		Mage::helper('datavalidate')->sendSlackMessage('export_orders', $website . "\n" . $debugMessage);
	}

}


$executionTime = array();
function getColValue($order, $col)
{

	global $executionTime;

	$start = microtime(true);

	global $config;

	$value = '';
	if ($col['type'] == 'default')
	{
		$value = $col['value'];
	}
	elseif ($col['type'] == 'dynamic')
	{
		$value = $col['function']($order, $col['params']);
	}

	/*if ($config['remove_file_delimiter'])
		$value = str_replace($config['file_delimiter'], '_', $value);*/

	//Remove extra chars (Max of ERP import)
	if (isset($col['maxsize']))
		$value = substr($value, 0, $col['maxsize']);

	$time_elapsed_us = microtime(true) - $start;

	if (!isset($executionTime[$col['function']]))
	{
		$executionTime[$col['function']] = array('count' => 0, 'time' => 0);
	}

	$executionTime[$col['function']]['count']++;
	$executionTime[$col['function']]['time'] += $time_elapsed_us;

	return $value . $config['file_delimiter'];
}

function getSalesRep($order, $params = array())
{
	//return '1301';
	$customer = $order->getCustomerId();

	if ($customer) {

		$customer = Mage::getModel('customer/customer')
			->load($customer);

		$id = Mage::getModel('fvets_salesrep/salesrep')
			->load($customer->getFvetsSalesrep())
			->getIdErp();

		if ($id > 0 || trim($id) != '')
		{
			return $id;
		}
		else
		{
			throw new Exception('Representante sem ID ERP');
		}


	}
}

function getOrderId($order, $params = array())
{
	return $order->getId();
}

function getOrderIdErp($order, $params = array())
{
	return $order->getIdErp();
}

function getIncrementId($order, $params = array())
{
	return $order->getIncrementId();
	//return $order->getId();
}

function getCustomerIdErp($order, $params = array())
{
	$id =  Mage::getModel('customer/customer')
		->load($order->getCustomerId())
		->getIdErp();

	if ($id > 0)
	{
		return $id;
	}
	else
	{
		throw new Exception('Cliente sem ID ERP');
	}
}

function getOrderDate($order, $params = array())
{
	return $order->getCreatedAtStoreDate()->toString($params['format']);
}

function getOrderUpdateDate($order, $params = array())
{
	$commentsCollection = $order->getStatusHistoryCollection();
	$commentsCollection->getSelect()->order('entity_id DESC')->limit(1);

	foreach ($commentsCollection as $comment)
	{
		return array_shift(explode(' ', $comment->getCreatedAt()));
	}
}

function getOrderStatus($order, $params)
{
	switch($order->getStatus())
	{
		case 'pending': return '2';
			break;
		/*case 'processing': return '2';
			break;*/
		case 'canceled': return '5';
			break;
		case 'returned': return '6';
			break;
		case 'complete': return '7';
			break;
		default : return null;
	}
}

function getOrderItensQty($order, $params = array())
{
	$qty = 0;
	foreach($order->getAllItems() as $item)
	{
		$qty += 1;
	}
	return $qty;
}

function getOrderIdTabela($order, $param = array())
{
	return getAllOrderTableprice($order)[0]['tableprice'];
}

function getCustomerNote($order, $params = array())
{
	$comments = $order->getStatusHistoryCollection();
	$comment = NULL;
	foreach($comments as $hist_cmt)
	{
		$comment .= $hist_cmt->getComment();
	}
	return str_replace(array("\t", "\r", "\n"), ';', $comment);
}

function getCustomerCpf($order, $params = array())
{
	$cpf = Mage::getModel('customer/customer')
		->load($order->getCustomerId())
		->getCpf();

	if (isset($params['onlynumber']))
		return preg_replace('/\D/', '', $cpf);
	else
		return $cpf;
}

function getCustomerName($order, $params = array())
{
	$customer = Mage::getModel('customer/customer')
		->load($order->getCustomerId());

	return $customer->getFirstname() . ' ' . $customer->getLastname();
}

function getCustomerStreet($order, $params = array())
{
	$address = 	Mage::getModel('sales/order_address')->load($order->getBillingAddressId());
	$street = $address->getStreet();
	if ($params['line'] == 0) {
		return $street[0] . ' ' . $street[1];
	} else {
		return $street[$params['line']];
	}
}

function getCustomerDistrict($order, $params = array())
{
	$address = 	Mage::getModel('sales/order_address')->load($order->getBillingAddressId());
	return $address->getBairro();
}

function getCustomerCep($order, $params = array())
{
	$address = 	Mage::getModel('sales/order_address')->load($order->getBillingAddressId());
	return $address->getPostcode();
}

function getCustomerEmail($order, $params = array())
{
	if ($order->getCustomerId() != NULL)
	{
		$customer = Mage::getModel('customer/customer')
			->load($order->getCustomerId());
		return $customer->getEmail();
	}
	else
	{
		return $order->getCustomerEmail();
	}
}

function getCustomerPhones($order, $params = array())
{
	$phone  = Mage::getModel('customer/customer')
		->load($order->getCustomerId())
		->getTelefone();

	$mobile = 	Mage::getModel('sales/order_address')
		->load($order->getBillingAddressId())
		->getMobilephone();

	return $phone . '/' . $mobile;
}

function getCustomerCity($order, $params = array())
{
	$address = 	Mage::getModel('sales/order_address')->load($order->getBillingAddressId());
	return $address->getCity();
}

function getCustomerRegion($order, $params = array())
{
	$region_id = Mage::getModel('sales/order_address')->load($order->getBillingAddressId())->getRegionId();
	return Mage::getModel('directory/region')->load($region_id)->getCode();
}

$paymentConditions = array();
function getGwap($order, $param)
{
	global $paymentConditions;

	if (!$paymentConditions[$order->getId()])
		$paymentConditions[$order->getId()] = Mage::getModel('gwap/order')->load($order->getId(), 'order_id');

	return $paymentConditions[$order->getId()];
}

function getPaymentCondition($order, $param)
{
	$gwap = getGwap($order, $param);
	if (NULL != $gwap->getId()) {
		$data = new Varien_Object(unserialize(Mage::helper('core')->decrypt($gwap->getInfo())));
		if (NULL != $data->getConditionData()) {
			return $data->getConditionData()['id_erp'];
		}
	}
	return null;
}

function getPaymentMethod($order, $param)
{
	$gwap = getGwap($order, $param);
	if (NULL != $gwap->getId())
	{
		$data = new Varien_Object(unserialize(Mage::helper('core')->decrypt($gwap->getInfo())));
		return $data->getIdErp();
	}
	return null;
}

function getPaymentId($order, $param)
{
	global $readConnection;

	$sql = "SELECT entity_id as id FROM `sales_flat_order_payment` WHERE `parent_id` = " . $order->getId();

	return $readConnection->query($sql)->fetch()['id'];
}

function getProductCode($order, $param = array())
{
	return $param['item']->getProduct()->getSku();
}

function getProductIdErp($order, $param = array())
{
	return $param['item']->getProduct()->setStoreId($order->getStoreId())->getIdErp();
}

function getProductIdTabela($order, $param = array())
{
	global $readConnection;

	$sql = 'SELECT tableprice FROM sales_flat_order_item WHERE item_id = ' .$param['item']->getId();

	$result = $readConnection->query($sql);

	return $result->fetchColumn();
}

function getProductType($order, $param = array())
{
	if ($param['item']->getPrice() > 0)
	{
		if ($param['item']->getUsesCredits())
		{
			return '3';
		}
		return '1';
	}
	else
	{
		return '2';
	}
}

function getProductQty($order, $param = array())
{
	return (int) $param['item']->getQtyOrdered();
}

function getProductDiscount($order, $param = array())
{
	return  number_format($param['item']->getDiscountAmount(),2, '.', '');
}

function getProductDiscountPercent($order, $param = array())
{
	$percentage = $param['item']->getDiscountPercent();

	if ($param['remove_payment_discount'])
		$percentage = ($percentage - $order->getPaymentDiscount() > 0) ? $percentage - $order->getPaymentDiscount() : 0;

	if ($param['decimal'])
		$percentage = $percentage / 100;

	if ($param['ignore_format'])
		return  $percentage;
	else
		return  number_format($percentage,2, '.', '');
}

function getProductPrice($order, $param = array())
{
	if ($param['item']->getUsesCredits())
	{
		return 0.00;
	}
	return number_format($param['item']->getPrice(), 2, '.', '');
}

function getProductFinalPrice($order, $param = array())
{
	if ($param['item']->getUsesCredits())
	{
		return 0.00;
	}
	return number_format($param['item']->getRowTotalInclTax() - $param['item']->getDiscountAmount(), 2, '.', '');
}

function getProductIdSalesrep($order, $param)
{
	global $readConnection;

	$sql = 'SELECT salesrep_id FROM sales_flat_order_item WHERE item_id = ' .$param['item']->getId();
	$result = $readConnection->query($sql);
	$salesrepId = $result->fetchColumn();

	$salesrep = Mage::getModel('fvets_salesrep/salesrep')->load($salesrepId);

	return $salesrep->getIdErp();
}

/**
 * Pega todos os representantes do pedido
 * @param $order
 */
function getAllOrderSalesrep($order)
{
	global $readConnection;

	$sql = 'SELECT DISTINCT(salesrep_id) as salesrep_id FROM sales_flat_order_item WHERE order_id = ' .$order->getId();

	$result = $readConnection->query($sql);

	return $result->fetchAll();
}

/**
 * Pega todas as tabelas do pedido
 * @param $order
 */
function getAllOrderTableprice($order)
{
	global $readConnection;

	$sql = 'SELECT DISTINCT(tableprice) as tableprice FROM sales_flat_order_item WHERE order_id = ' .$order->getId();

	$result = $readConnection->query($sql);

	return $result->fetchAll();
}

function sendEmailForLackOfSalesrep($order)
{
	try {
		$emailTemplate = Mage::getModel('core/email_template');
		$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom2/email', 1));
		$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom2/name', 1));
		$emailTemplate->setTemplateSubject('URGENTE: :( ' . Mage::app()->getWebsite()->getName() . ' - Pedido não têm todos os representantes necessários para ser exportado.');

		$message = '<table border="1">';
		$message .= '<tr>';
		foreach ($order->getData() as $key => $value) {
			$message .= '<th>';
			$message .= $key;
			$message .= '</th>';
		}
		$message .= '</tr>';
		$message .= '<tr>';
		foreach ($order->getData() as $key => $value) {
			$message .= '<td>';
			if (!is_array($value) && !is_object($value))
				$message .= $value;
			$message .= '</td>';
		}
		$message .= '</tr>';
		$message .= '</table>';

		$emailTemplate->setTemplateText($message);

		$emailTemplate->send('ti+errors@4vets.com.br', 'TI', array());

	} catch(Exception $e)
	{
		echo $e->getMessage();
	}
}

function sendEmailForLackOfTableprice($order)
{
	try {
		$emailTemplate = Mage::getModel('core/email_template');
		$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom2/email', 1));
		$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom2/name', 1));
		$emailTemplate->setTemplateSubject('URGENTE: :( ' . Mage::app()->getWebsite()->getName() . ' - Pedido não têm todas as tabelas de preço necessárias para ser exportado.');

		$message = '<table border="1">';
		$message .= '<tr>';
		foreach ($order->getData() as $key => $value) {
			$message .= '<th>';
			$message .= $key;
			$message .= '</th>';
		}
		$message .= '</tr>';
		$message .= '<tr>';
		foreach ($order->getData() as $key => $value) {
			$message .= '<td>';
			if (!is_array($value) && !is_object($value))
				$message .= $value;
			$message .= '</td>';
		}
		$message .= '</tr>';
		$message .= '</table>';

		$emailTemplate->setTemplateText($message);

		$emailTemplate->send('ti+errors@4vets.com.br', 'TI', array());

	} catch(Exception $e)
	{
		echo $e->getMessage();
	}
}
