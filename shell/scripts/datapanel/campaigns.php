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

	$rulesCollection = Mage::getModel('salesrule/rule')->getCollection();
	$rulesCollection->getSelect()
		->join(array('sw' => 'salesrule_website'), 'sw.rule_id = main_table.rule_id and sw.website_id = ' . $websiteId);

	$rulesCollection->getSelect()->__toString();

	toFile($websiteCode, 'campaigns', collectionToStringFormated($rulesCollection));
}