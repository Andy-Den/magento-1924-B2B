<?php

class FVets_Catalog_Block_Category_View extends Mage_Catalog_Block_Category_View
{
	public function getCurrentCategory()
	{
		if (!$this->hasData('current_category'))
		{
			$currentCategory = Mage::registry('current_category');
			if (!$currentCategory)
			{
				$currentCategory = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId());
			}
			$this->setData('current_category', $currentCategory);
		}
		return $this->getData('current_category');
	}

	public function getCategoryName()
	{
		if (in_array('attributemenu_index_index', Mage::app()->getLayout()->getUpdate()->getHandles()))
		{
			$attribute = Mage::helper('fvets_attributemenu')->getAttributeValue();
			return $attribute->getValue();
		}

		$_category = $this->getCurrentCategory();
		return $this->helper('catalog/output')->categoryAttribute($_category, $_category->getName(), 'name');
	}
}
