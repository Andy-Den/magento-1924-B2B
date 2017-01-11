<?php

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';
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
		$reps = Mage::getModel('fvets_salesrep/salesrep')->getCollection()
			->addStoreToFilter($store->getId());

		foreach ($reps as $rep) {
			$arrayTmp = array("store_code" => $store->getCode(), "store_name" => (count($stores) > 1 ? "[" . $website->getName() . "] " . $store->getName() : $store->getName()), "rep_name" => $rep->getname(), "active" => $rep->getStatus(), "brands_qty" => count($rep->getSelectedCategoriesCollection(false, $store->getId())));
			$arrayReturn['distributors'][] = $arrayTmp;
		}
	}
}

die(json_encode($arrayReturn));

function getRepActivity($store)
{
	$return = array();
	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $store->getWebsiteId())
		->addFieldToFilter('mp_cc_is_approved', 1);
	$return['approved'] = count($customers);
	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $store->getWebsiteId())
		->addFieldToFilter('mp_cc_is_approved', 0);
	$return['not_approved'] = count($customers);
	return $return;
}

function getTotalAllinActiveCostumers($store)
{
	$return = array();
	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $store->getWebsiteId())
		->addAttributeToFilter('fvets_allin_status', 'V', 'left');
	$return['active'] = count($customers);
	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $store->getWebsiteId())
		->addAttributeToFilter('fvets_allin_status', 'I', 'left');
	$return['inactive'] = count($customers);
	return $return;
}

function getTotalActivedCostumers($store)
{
	$return = array();
	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $store->getWebsiteId())
		->addFieldToFilter('is_active', '1');

	$return['active'] = count($customers);

	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $store->getWebsiteId())
		->addFieldToFilter('is_active', '0');

	$return['inactive'] = count($customers);

	return $return;
}

function getTotalHasReps($store)
{
	$return = array();
	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $store->getWebsiteId())
		->addAttributeToFilter('id_erp', array('notnull' => true), 'left');
	$return['has_rep'] = count($customers);

	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $store->getWebsiteId())
		->addAttributeToFilter('id_erp', array('null' => true), 'left');
	$return['has_no_rep'] = count($customers);

	return $return;
}
