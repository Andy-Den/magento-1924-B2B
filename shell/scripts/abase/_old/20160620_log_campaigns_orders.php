<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/21/15
 * Time: 3:59 PM
 */

//ini_set('memory_limit', '2048M');

$local = true;

$path = __DIR__; //'/wwwroot/current/shell/scripts/datavalidate/';

if ($local == true) {
	$requiredFile = './configScript.php';
} else {
	$requiredFile = $path . DIRECTORY_SEPARATOR . 'config.php';
}

require_once $requiredFile;

$customerDeniedEmails = array('%@4vets.com.br%');

$channel = 'datavalidate';
$filename = date('Ymd') . '-relatorioOrders.csv';
$filePath = Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'datavalidate' . DS . 'extras' . DS . $filename;

$fromDate = (date("Y-m-d 00:00:01", strtotime("-10000 days")));

//$fromDate = (date("Y-m-d 00:00:01", strtotime("-30 days")));

$toDate = new DateTime("-1 days", new DateTimeZone('America/Sao_Paulo'));
$toDate->setTime(23, 59, 59);
$toDate->setTimezone(new DateTimeZone('UTC'));
$toDate = $toDate->format('Y-m-d H:i:s');

$finalResult = "";
$finalResult .= ($header . "\n");

$totalOrders = 0;

$customer = array();
$salesrep = array();
$storeview = array();
$product = array();
$category = array();

$lastStoreId = 0;
$websitesId = array(8);
foreach ($websitesId as $websiteId) {
	$storesid = getStoreidsByWebsiteId($websiteId);

	$dailyOrders = Mage::getModel('sales/order')->getCollection()
		->addFieldtoFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate))
		->addFieldtoFilter('status', array('neq' => 'canceled'))
		->addFieldtoFilter('store_id', array('in' => $storesid));

	foreach ($customerDeniedEmails as $customerDeniedEmail) {
		$dailyOrders->addFieldToFilter('customer_email', array('nlike' => $customerDeniedEmail));
	}

	foreach ($dailyOrders as $order) {
		if (!$order->getDiscountDescription()) {
			continue;
		}
		echo "Order|" . $order->getIncrementId() . "|" . $order->getDiscountDescription() . "\n";

		if ($lastStoreId != $order->getStoreId()) {
			Mage::app()->setCurrentStore($order->getStoreId());
			$lastStoreId = $order->getStoreId();
		}

		$customerId = $order->getCustomerId();

		//Retrieve Customer
		if (!key_exists($customerId, $customer)) {
			$customer[$customerId] = Mage::getModel('customer/customer')->load($customerId);
		}

		//Retrieve Storeview
		if (!key_exists($order->getStoreId(), $storeview)) {
			$storeview[$order->getStoreId()] = Mage::getModel('core/store')->load($order->getStoreId());
		}
		$storeViewName = $storeview[$order->getStoreId()]->getName();

		foreach ($order->getAllVisibleItems() as $item) {
			if (!$item->getAppliedRuleIds()) {
				continue;
			}
			echo "Item|" . $item->getOrder()->getIncrementId() . "|" . $item->getAppliedRuleIds() . "\n";
		}
	}
}

if (file_put_contents($path . DS . 'extras' . DS . $filename, $finalResult)) {
	//Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), $totalOrders . " - Total de vendas", $filePath, 'csv', $channel);
} else {
	$errorMsg = "Arquivo " . $filename . " não salvo";
	echo $errorMsg . "\n";
	//Mage::helper('datavalidate')->sendSlackMessage($channel, $errorMsg);
}

if (file_exists($filePath)) {
	//unlink($filePath);
} else {
	// code when file not found
}

function getStoreidsByWebsiteId($websiteId)
{
	$collection = Mage::getModel('core/store')->getCollection()
		->addFieldToFilter('website_id', $websiteId);
	return $collection->toOptionArray();
}

function getWebsitesIds()
{
	global $deniedWebsites;
	$websites = Mage::getModel('core/website')->getCollection()
		->addFieldToSelect('website_id')
		->addFieldToFilter('website_id', array('nin' => $deniedWebsites));
	$websites = $websites->toOptionArray();
	$arrayReturn = array();
	foreach ($websites as $website) {
		$arrayReturn[] = $website['value'];
	}
	return $arrayReturn;
}

function sendMail($toEmail, $toName, $subject, $message)
{
	try {
		$emailTemplate = Mage::getModel('core/email_template');
		$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom2/email', 1));
		$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom2/name', 1));
		$emailTemplate->setTemplateSubject($subject);
		$emailTemplate->setTemplateText($message);

		$emailTemplate->send($toEmail, $toName, array());

		return true;
	} catch (Exception $ex) {
		return $ex->__toString();
	}
}