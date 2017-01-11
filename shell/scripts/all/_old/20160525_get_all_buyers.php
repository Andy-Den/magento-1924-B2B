<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/25/16
 * Time: 10:46 AM
 */

require_once './configScript.php';
$customerDeniedEmails = array('%@4vets.com.br%');
$websitesId = array(15, 12, 11, 9, 10);

foreach ($websitesId as $websiteId) {
	$storesid = getStoreidsByWebsiteId($websiteId);

	$dailyOrders = Mage::getModel('sales/order')->getCollection()
		->addFieldtoFilter('status', array('neq' => 'canceled'))
		->addFieldtoFilter('store_id', array('in' => $storesid));

	foreach ($customerDeniedEmails as $customerDeniedEmail) {
		$dailyOrders->addFieldToFilter('customer_email', array('nlike' => $customerDeniedEmail));
	}

	foreach ($dailyOrders as $order) {
		$customerId = $order->getCustomerId();
		$customer = Mage::getModel('customer/customer')->load($customerId);
		echo $customer->getName() . "|" . $customer->getId() . "|" . $customer->getPasswordHash() . "\n";
	}
}

function getStoreidsByWebsiteId($websiteId)
{
	$collection = Mage::getModel('core/store')->getCollection()
		->addFieldToFilter('website_id', $websiteId);
	return $collection->toOptionArray();
}