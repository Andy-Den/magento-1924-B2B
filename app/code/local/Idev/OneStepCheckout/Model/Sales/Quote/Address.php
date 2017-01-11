<?php

if(Mage::helper('core')->isModuleEnabled('FVets_CheckoutSplit')){
	class Idev_OneStepCheckout_Model_Sales_Quote_Address_Tmp extends FVets_CheckoutSplit_Model_Sales_Quote_Address {}
} else if(Mage::helper('core')->isModuleEnabled('FVets_Sales')){
	class Idev_OneStepCheckout_Model_Sales_Quote_Address_Tmp extends FVets_Sales_Model_Sales_Quote_Address {}
} else if(Mage::helper('core')->isModuleEnabled('Amasty_Promo')){
	class Idev_OneStepCheckout_Model_Sales_Quote_Address_Tmp extends Amasty_Promo_Model_Sales_Quote_Address {}
} else if(Mage::helper('core')->isModuleEnabled('Allpago_Installments')){
	class Idev_OneStepCheckout_Model_Sales_Quote_Address_Tmp extends Allpago_Installments_Model_Address {}
} else {
	class Idev_OneStepCheckout_Model_Sales_Quote_Address_Tmp extends Mage_Sales_Model_Quote_Address {}
}

/**
 * Class Idev_OneStepCheckout_Model_Sales_Quote_Address
 */
class  Idev_OneStepCheckout_Model_Sales_Quote_Address extends  Idev_OneStepCheckout_Model_Sales_Quote_Address_Tmp
{
	public function getFirstname() {
		if (Mage::helper('onestepcheckout')->clearDash(parent::getFirstname()) == '')
		{
			return $this->getQuote()->getCustomer()->getFirstname();
		} else {
			return parent::getFirstname();
		}
	}

	public function getLastname() {
		if (Mage::helper('onestepcheckout')->clearDash(parent::getLastname()) == '')
		{
			return $this->getQuote()->getCustomer()->getLastname();
		}
		else
		{
			return parent::getLastname();
		}
	}

	public function getTelephone() {
		if (Mage::helper('onestepcheckout')->clearDash(parent::getTelephone()) == '')
		{
			return $this->getQuote()->getCustomer()->getTelefone();
		}
		else
		{
			return parent::getTelephone();
		}
	}
}
?>