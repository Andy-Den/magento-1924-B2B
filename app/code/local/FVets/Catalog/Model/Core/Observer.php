<?php
require_once(Mage::getBaseDir('lib') . '/FVets/functions.php');
class FVets_Catalog_Model_Core_Observer
{
	public function getLastViewedCategoryLevel($observer)
	{
		$category = $observer->getCategory();
		Mage::getSingleton('catalog/session')->setLastViewedCategoryLevel($category->getLevel());
	}

	public function addCategoryLevelHandle($observer)
	{
		if ($observer->getAction() instanceof Mage_Catalog_CategoryController)
		{
			$layout = Mage::getSingleton('core/layout');
			$layout->getUpdate()->addHandle('catalog_category_level_'.convert_number_to_words(Mage::getSingleton('catalog/session')->getLastViewedCategoryLevel()));
		}
	}
}