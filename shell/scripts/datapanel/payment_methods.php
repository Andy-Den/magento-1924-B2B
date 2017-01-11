<?php
require_once 'config.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/30/15
 * Time: 10:40 AM
 */

$reportName = 'payment_methods';

foreach ($websitesId as $websiteId) {
	$websiteCode = getWebsiteCode($websiteId);

	$storeIds = getStoreidsByWebsiteId($websiteId);

	$allActivePaymentMethods = array();

	foreach($storeIds as $store) {
		$allActivePaymentMethods = array_merge($allActivePaymentMethods, Mage::getModel('payment/config')->getActiveMethods($store['value']));
	}

	$result = array();
	foreach($allActivePaymentMethods as $method) {
		$result[] = $method->getId();
	}

	//$salesCollection->getSelect()->__toString();

	toFile($websiteCode, $reportName, collectionToStringFormated($result));
}