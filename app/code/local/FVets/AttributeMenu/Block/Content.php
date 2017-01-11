<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/24/15
 * Time: 10:57 AM
 */
class FVets_AttributeMenu_Block_Content extends Mage_Page_Block_Html_Topmenu
{
	public function _construct()
	{
		parent::_construct();
	}

	public function getCacheKeyInfo()
	{
		$params = Mage::app()->getRequest()->getParams();
		$paramsKey = '';
		foreach($params as $key => $value) {
			$paramsKey = $paramsKey . ($key.$value);
		}
		return array(
			'fvets_attributemenu_block_content',
			$paramsKey,
			Mage::app()->getStore()->getCode(),
			Mage::getSingleton("customer/session")->isLoggedIn()
		);
	}

	public function getGroupedBrandsByAttribute()
	{
		$attribute = Mage::helper('fvets_attributemenu')->getAttribute();
		$parentCategoryId = Mage::getModel('rootcategory/store_group')->getRootCategoryId();
		$productAttributeValue = Mage::app()->getRequest()->getParam($attribute->getAttributeCode());

		$brands = Mage::getModel('catalog/category')
			->getCollection()
			->addFieldToFilter('parent_id', array('eq' => $parentCategoryId))
			->addFieldToFilter('is_active', array('eq' => '1'))
			->addAttributeToSelect('*');
		$brands = Mage::helper('fvets_attributemenu')->filterCategoriesByProductAttribute($brands, $attribute->getAttributeId(), $productAttributeValue);

		$allowedBrands = Mage::helper('fvets_salesrep/customer')->getAllowedCategories();

		if (!Mage::registry('allowAllCategories')) {
			Mage::register('allowAllCategories', true);
		}

		foreach ($brands as $brand)
		{
			$brand->setIsAvailableForBuying(0);
			foreach ($allowedBrands as $allowedBrand)
			{
				if ($allowedBrand->getEntityId() == $brand->getEntityId())
				{
					$brand->setIsAvailableForBuying(1);
					break;
				}
			}
		}
		return $brands;
	}

}