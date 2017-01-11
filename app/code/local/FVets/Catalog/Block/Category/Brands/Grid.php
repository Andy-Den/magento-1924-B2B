<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 11/10/15
 * Time: 11:34 AM
 */
class FVets_Catalog_Block_Category_Brands_Grid extends Mage_Core_Block_Template
{
	protected function _construct()
	{
		$this->addData(array('cache_lifetime' => false));
		$this->addCacheTag(array(
			Mage_Catalog_Model_Category::CACHE_TAG,
			Mage_Core_Model_Store_Group::CACHE_TAG
		));
	}

	/**
	 * Get Key pieces for caching block content
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		$shortCacheId = array(
			'CATALOG_CATEGORY_GRID',
			Mage::app()->getStore()->getId(),
			Mage::getSingleton('customer/session')->getCustomer()->getFvetsSalesrep(),
			Mage::getSingleton('customer/session')->isLoggedIn()
		);

		$params = Mage::app()->getRequest()->getParams();
		if ($params) {
			$output = implode(', ', array_map(function ($v, $k) { return sprintf("%s='%s'", $k, $v); }, $params, array_keys($params)));
			$shortCacheId[] = $output;
		}

		$shortCacheId = implode('|', $shortCacheId);
		$shortCacheId = md5($shortCacheId);
		$cacheId['short_cache_id'] = $shortCacheId;
		return $cacheId;
	}

	public function getStoreCategories($sorted=false, $asCollection=false, $toLoad=true)
	{
		$helper = Mage::helper('catalog/category');
		return $helper->getStoreCategories($sorted, $asCollection, $toLoad);
	}

	public function isCategoryActive($category)
	{
		return $this->getCurrentCategory()
			? in_array($category->getId(), $this->getCurrentCategory()->getPathIds()) : false;
	}

	public function getCurrentCategory()
	{
		if (Mage::getSingleton('catalog/layer'))
		{
			return Mage::getSingleton('catalog/layer')->getCurrentCategory();
		}
		return false;
	}
}