<?php

class FVets_Salesrep_Block_Product_List extends Mage_Page_Block_Html
{

	private $_product = null;

	public function getSalesreps()
	{
		$category = Mage::registry('current_product')->getCategory();

		if (!$category) {
			$categoryIds = Mage::registry('current_product')->getCategoryIds();
			$category = Mage::getModel('catalog/category')->getCollection()
			->addFieldToFilter('level', '2')
			->addIdFilter($categoryIds)
			->getFirstItem();
		}

		while($category->getLevel() > 2)
		{
			$category = $category->getParentCategory();
		}
		$collection =  Mage::getModel('fvets_salesrep/salesrep')->getCollection()
			->addStoreToFilter(Mage::app()->getStore()->getId())
			->addCategoryFilter($category)
		;

		return $collection;
	}

	protected function getProduct()
	{
		if ($this->_product === null)
		{
			$this->_product = Mage::registry('product');
		}

		return $this->_product;
	}
}

?>