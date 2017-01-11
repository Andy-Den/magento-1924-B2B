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

	$storeIds = getStoreidsByWebsiteId($websiteId);
	$storeIdsFormated = '';
	foreach($storeIds as $store) {
		$storeIdsFormated .= ($store['value'] . ',');
	}
	$storeIdsFormated = rtrim($storeIdsFormated, ',');

	$query = "select distinct(lc.customer_id) from log_customer lc where lc.store_id in (" . $storeIdsFormated . ")";
	$loggedinCustomers = $readConnection->fetchAll($query);

	//$salesCollection->getSelect()->__toString();

	toFile($websiteCode, 'logged_users', collectionToStringFormated($loggedinCustomers));
}