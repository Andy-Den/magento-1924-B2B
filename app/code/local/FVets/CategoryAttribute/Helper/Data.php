<?php

class FVets_CategoryAttribute_Helper_Data extends Mage_Core_Helper_Abstract
{

	function getCategoryByUrlkey($parentCategoryCode, $childCode = null)
	{
		if (!$parentCategoryCode)
		{
			return null;
		}

		$categoryChildrenCol = Mage::getResourceModel('catalog/category_collection')
			->addAttributeToFilter('url_key', $parentCategoryCode)
			->getFirstItem()
		;

		if (!$childCode)
		{
			return $categoryChildrenCol;
		}

		foreach($categoryChildrenCol->getChildrenCategories() as $childCategory) {
			if ($childCategory->getUrlKey() == $childCode) {
				return $childCategory;
			}
		}
		return null;
	}

}
