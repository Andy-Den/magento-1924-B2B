<?php
class  Idev_OneStepCheckout_Model_Sales_Service_Quote extends Mage_Sales_Model_Service_Quote
{

	/**
	 * Validate quote data before converting to order
	 *
	 * @return Mage_Sales_Model_Service_Quote
	 */
	protected function _validate()
	{
		if (!$this->getQuote()->isVirtual()) {
			$address = $this->getQuote()->getShippingAddress();

			if (Mage::getStoreConfig(Idev_OneStepCheckout_Helper_Data::XML_GENERAL_VALIDATE_CHECKOUT_CUSTOMER))
			{
				$addressValidation = $address->validate();
				if ($addressValidation !== true) {
					Mage::throwException(
						Mage::helper('sales')->__('Please check shipping address information. %s', implode(' ', $addressValidation))
					);
				}
			}

			$method= $address->getShippingMethod();
			$rate  = $address->getShippingRateByCode($method);
			if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
				Mage::throwException(Mage::helper('sales')->__('Please specify a shipping method.'));
			}
		}

		if (Mage::getStoreConfig(Idev_OneStepCheckout_Helper_Data::XML_GENERAL_VALIDATE_CHECKOUT_CUSTOMER))
		{
			$addressValidation = $this->getQuote()->getBillingAddress()->validate();
			if ($addressValidation !== true) {
				Mage::throwException(
					Mage::helper('sales')->__('Please check billing address information. %s', implode(' ', $addressValidation))
				);
			}
		}

		if (!($this->getQuote()->getPayment()->getMethod())) {
			Mage::throwException(Mage::helper('sales')->__('Please select a valid payment method.'));
		}

		return $this;
	}

}