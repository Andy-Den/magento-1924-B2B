<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/19/15
 * Time: 12:21 PM
 */
require_once 'config.php';
$reportName = 'payment_conditions_special_customers';

foreach ($websitesId as $websiteId) {
	$websiteCode = getWebsiteCode($websiteId);
	$storeIds = getStoreidsByWebsiteId($websiteId);

	$query = 'SELECT ce.entity_id as customer_id, fpc.*
FROM customer_entity ce
JOIN fvets_payment_condition_customer fpcc ON fpcc.customer_id = ce.entity_id
JOIN fvets_payment_condition fpc ON fpc.entity_id = fpcc.condition_id and fpc.`status` = 1 and fpc.apply_to_all = 0
WHERE ce.website_id = ' . $websiteId;

	$collection = $readConnection->fetchAll($query);

	toFile($websiteCode, $reportName, collectionToStringFormated($collection));
}