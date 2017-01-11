<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 3/15/16
 * Time: 3:54 PM
 */

require_once './configScript.php';

$storeId = 1;
$websiteId = 1;
$products = Mage::getModel('catalog/product')->getCollection()
	->addWebsiteFilter($websiteId)
	->addAttributeToFilter('type_id', 'simple');

foreach ($products as $product) {
	if ($product->getSku()) {

		//echo $product->getSku() . "\n";

		$summaryData = Mage::getModel('review/review_summary')
			->setStoreId($storeId)
			->load($product->getId());
		if ($summaryData['rating_summary']) {
			//echo $summaryData['rating_summary'];
			$product->load();
			echo $product->getName() . "\n";
		}
	}
}
