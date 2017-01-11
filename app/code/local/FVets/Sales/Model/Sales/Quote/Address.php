<?php

if(Mage::helper('core')->isModuleEnabled('Amasty_Promo')){
	class FVets_Sales_Model_Sales_Quote_Address_Tmp extends Amasty_Promo_Model_Sales_Quote_Address {}
} else if(Mage::helper('core')->isModuleEnabled('Allpago_Installments')){
	class FVets_Sales_Model_Sales_Quote_Address_Tmp extends Allpago_Installments_Model_Address {}
} else {
	class FVets_Sales_Model_Sales_Quote_Address_Tmp extends Mage_Sales_Model_Quote_Address {}
}

class FVets_Sales_Model_Sales_Quote_Address extends FVets_Sales_Model_Sales_Quote_Address_Tmp
{
	protected $_discountType = array();

	protected $_increaseType = array();

	public function addDiscountType($code, $type)
	{
		$this->_discountType[$code] = $type;
	}

	public function addIncreaseType($code, $type)
	{
		$this->_increaseType[$code] = $type;
	}

	public function getDiscountType($code)
	{
		if (isset($this->_discountType[$code]))
		{
			return $this->_discountType[$code];
		}

		return null;
	}

	public function getIncreaseType($code)
	{
		if (isset($this->_increaseType[$code]))
		{
			return $this->_increaseType[$code];
		}

		return null;
	}
}