<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/28/15
 * Time: 1:20 PM
 */

require_once 'config.php';

$notAllowedCustomers = array(1629, 1588, 958);

foreach ($websitesId as $websiteId) {
	$website = Mage::getModel('core/website')->load($websiteId);
	echo "\n\n\n" . $website->getName() . "\n\n\n";

	$allOrders = Mage::getModel('sales/order')->getCollection()
		->addFieldToFilter('customer_id', array('nin' => $notAllowedCustomers))
		->addFieldToFilter('store_id', array('in' => getStoreIdsByWebsite($websiteId)));

	foreach ($allOrders as $order) {
		$incrementId = $order->getIncrementId();
		$customerId = $order->getCustomerId();
		$customer = Mage::getModel('customer/customer')->load($customerId);
		$repId = explode(',', $customer->getFvetsSalesrep())[0];
		$rep = Mage::getModel('fvets_salesrep/salesrep')->load($repId);
		echo $incrementId . '|' . ($customer->getRazaoSocial() ? $customer->getRazaoSocial() : $customer->getName()) . '|' . $rep->getName() . "\n";
	}

}

function getStoreIdsByWebsite($websiteId)
{
	$collection = Mage::getModel('core/store')->getCollection()
		->addFieldToFilter('website_id', $websiteId)
		->load();
	return $collection->toOptionArray();
}