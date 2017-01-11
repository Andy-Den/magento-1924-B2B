<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 11/24/15
 * Time: 6:31 PM
 */

require_once './configScript.php';

$products = Mage::getModel('catalog/product')->getCollection()->addWebsiteFilter($websiteId);

foreach ($products as $product)
{
	echo ($product->getSku() . "\n");
	$product->load();
	$product->setIdErp(null);
	$product->setStoreId(0);
	$product->save();
}



//apenas um produto
//$product = Mage::getModel('catalog/product')->loadByAttribute('sku', 'BAY10067');
//$product->load();
//$product->setIdErp(null);
//$product->setStoreId(0);
//$product->save();
//die();