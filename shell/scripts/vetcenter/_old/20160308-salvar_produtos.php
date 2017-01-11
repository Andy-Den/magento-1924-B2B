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
	$product->save();
}