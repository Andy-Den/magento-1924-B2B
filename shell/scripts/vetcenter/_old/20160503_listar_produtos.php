<?php

require_once './configScript.php';
$storeId =24;

$products = Mage::getModel('catalog/product')->getCollection()
	->addWebsiteFilter($websiteId);

foreach($products as $product) {
	$product->setStoreId($storeId)->load();
	if($product->getIdErp()) {
		if (!$product->getSku()) {
			echo $product->getIdErp() . "|" . $product->getName() . "\n";
		}
	}
}