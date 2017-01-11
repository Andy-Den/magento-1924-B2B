<?php

if(Mage::helper('core')->isModuleEnabled('FVets_Sales')){
	class FVets_CheckoutSplit_Model_Sales_Quote_Address_Tmp extends FVets_Sales_Model_Sales_Quote_Address {}
} else if(Mage::helper('core')->isModuleEnabled('Amasty_Promo')){
	class FVets_CheckoutSplit_Model_Sales_Quote_Address_Tmp extends Amasty_Promo_Model_Sales_Quote_Address {}
} else if(Mage::helper('core')->isModuleEnabled('Allpago_Installments')){
	class FVets_CheckoutSplit_Model_Sales_Quote_Address_Tmp extends Allpago_Installments_Model_Address {}
} else {
	class FVets_CheckoutSplit_Model_Sales_Quote_Address_Tmp extends Mage_Sales_Model_Quote_Address {}
}

class FVets_CheckoutSplit_Model_Sales_Quote_Address extends FVets_CheckoutSplit_Model_Sales_Quote_Address_Tmp
{

	public function getItemsCollection()
	{
		parent::getItemsCollection();

		$this->_items = Mage::helper('fvets_salesrep/quote')->getSalesrepItems($this->_items);

		return $this->_items;
	}

	public function getAllItems()
	{
		$items = parent::getAllItems();

		$items = Mage::helper('fvets_salesrep/quote')->getSalesrepItems($items);

		return $items;
	}

	public function getAllNonNominalItems()
	{
		$result = parent::getAllNonNominalItems();

		$result = Mage::helper('fvets_salesrep/quote')->getSalesrepItems($result);

		return $result;
	}

	public function getAllNominalItems()
	{
		$result = parent::getAllNominalItems();

		$result = Mage::helper('fvets_salesrep/quote')->getSalesrepItems($result);

		return $result;
	}

	public function getAllVisibleItems()
	{
		$items = parent::getAllVisibleItems();

		$items = Mage::helper('fvets_salesrep/quote')->getSalesrepItems($items);

		return $items;
	}

}