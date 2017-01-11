<?php

require_once '/wwwroot/whtielabel/current/shell/services/configService.php';
require_once '/wwwroot/whitelabel/current/shell/services/util/util.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 6/20/16
 * Time: 2:38 PM
 */

//return example json format: {"distributors":[{"name":"Peclam","products":10,"visible_products":5,"totalCustomers":1200}]}

$arrayReturn = array();
$websites = Mage::getModel('core/website')->getCollection();

$arrayReturn['distributors'] = array();

foreach ($websites as $website) {
	$stores = $website->getStores();
	foreach ($stores as $store) {
		$arrayTmp = array("store_code" => $store->getCode(), "store_name" => (count($stores) > 1 ? "[" . $website->getName() . "] " . $store->getName() : $store->getName()), "total_products" => getTotalProducts($store), "active_visible_products" => getVisibleProducts($store));
		$arrayReturn['distributors'][] = $arrayTmp;
	}
}

die(json_encode($arrayReturn));

function getTotalProducts($store)
{
	$products = Mage::getModel('catalog/product')->getCollection()
		->addWebsiteFilter($store->getWebsiteId());
	return count($products);
}

function getVisibleProducts($store)
{
	$products = Mage::getModel('catalog/product')->setStoreId($store->getId())->getCollection()
		->addWebsiteFilter($store->getWebsiteId())
		->addAttributeToFilter('status', 1)
		->addAttributeToFilter('visibility', 4);
	return count($products);
}
