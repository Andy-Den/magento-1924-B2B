<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/14/14
 * Time: 9:48 AM
 */

require_once 'abstract.php';

//1. ===== Adição de nova categoria, do Website, Store e Storeview =====

//Create root category
/* initiate installer */
/* start setup */
$category = Mage::getModel('catalog/category')->getCollection()
	->addFieldToFilter('name', $_toCategoryName)
	->getFirstItem();

$_categoryId = $category->getId();

if (!$_categoryId) {
	die('Nao ha categoria com o nome ' . $_toCategoryName);
}

//#addWebsite
/** @var $website Mage_Core_Model_Website */
if ($_createNewWebsite) {
	$website = Mage::getModel('core/website');
	$website->setCode($_toNewCode)
		->setName($_toWebSiteName)
		->setSortOrder(1)
		->save();
} else {
	$website = Mage::getModel('core/website')->load($_copyToWebsiteCode);
}

//#addStoreGroup
/** @var $storeGroup Mage_Core_Model_Store_Group */
if ($_createNewStore) {
	$storeGroup = Mage::getModel('core/store_group');
	$storeGroup->setWebsiteId($website->getId())
		->setName($_toStoreName)
		->setRootCategoryId($_categoryId)
		->save();
} else {
	$storeGroup = Mage::getModel('core/store_group')->getCollection()
		->addFieldToFilter('name', $_copyToStoreName)
		->getFirstItem();
}

//#addStore
/** @var $store Mage_Core_Model_Store */
$store = Mage::getModel('core/store');
$store->setCode($_toNewCode)
	->setWebsiteId($storeGroup->getWebsiteId())
	->setGroupId($storeGroup->getId())
	->setName($_toStoreViewName)
	->setIsActive(1)
	->save();