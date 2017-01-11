<?php
require_once 'config.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/30/15
 * Time: 10:40 AM
 */

foreach ($websitesId as $websiteId) {
	$websiteCode = getWebsiteCode($websiteId);

	$salesCollection = Mage::getModel('sales/order')->getCollection();
	$salesCollection->addFieldToFilter('store_id', array('in' => getStoreidsByWebsiteId($websiteId)));
	$salesCollection->addFieldToFilter('customer_email', array('nlike' => '%' . $deniedData['emailPattern']));

	$salesCollection->getSelect()->__toString();

	toFile($websiteCode, 'sales', collectionToStringFormated($salesCollection));
}