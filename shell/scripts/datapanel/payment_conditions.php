<?php
require_once 'config.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/30/15
 * Time: 10:40 AM
 */

$reportName = 'payment_conditions';

foreach ($websitesId as $websiteId) {
	$websiteCode = getWebsiteCode($websiteId);

	$storeIds = getStoreidsByWebsiteId($websiteId);
	$storeIdsFormated = '';
	foreach($storeIds as $store) {
		$storeIdsFormated .= ($store['value'] . ',');
	}
	$storeIdsFormated = rtrim($storeIdsFormated, ',');

	$query = "select * from fvets_payment_condition_store fpcs join fvets_payment_condition fpc on fpc.entity_id = fpcs.condition_id where fpcs.store_id in (" . $storeIdsFormated . ")";
	$collection = $readConnection->fetchAll($query);

	//$salesCollection->getSelect()->__toString();

	toFile($websiteCode, $reportName, collectionToStringFormated($collection));
}