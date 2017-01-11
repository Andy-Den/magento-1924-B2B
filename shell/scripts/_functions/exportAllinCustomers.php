<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 3/14/16
 * Time: 3:34 PM
 */

function getArrayAllowedCategories($websiteId)
{
	$website = Mage::getModel('core/website')->load($websiteId);
	$stores = $website->getStoreCollection()
		->addFieldToFilter('is_active', 1);

	$group = $website->load($websiteId)->getGroupCollection()->getFirstItem();
	$rootCategoryId = $group->getRootCategoryId();
	$categories = Mage::getModel('catalog/category')->getCollection()
		->addFieldToFilter('parent_id', $rootCategoryId);

	$categoriesArray = array();

	foreach ($categories as $category)
	{
		foreach ($stores as $store)
		{
			$category->setStoreId($store->getId())->load();
			if ($category->getIsActive())
			{
				$categoriesArray[$category->getId()] = $category;
			}
		}
	}
	return $categoriesArray;
}