<?php
class FVets_CheckoutSplit_Model_Sales_Quote extends Mage_Sales_Model_Quote
{

	private $lastItemsCacheKey = null;

	public function getItemsCollection($useCache = true)
	{
		parent::getItemsCollection($useCache);

		$this->_items = Mage::helper('fvets_salesrep/quote')->getSalesrepItems();

		return $this->_items;
	}

	public function getAllItems()
	{
		$items = parent::getAllItems();

		$items = Mage::helper('fvets_salesrep/quote')->getSalesrepItems($items);

		return $items;
	}

	public function getAllVisibleItems()
	{
		$items = parent::getAllVisibleItems();

		$items = Mage::helper('fvets_salesrep/quote')->getSalesrepItems($items);

		return $items;
	}



}