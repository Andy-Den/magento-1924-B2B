<?php

class FVets_CheckoutSplit_Block_Onestepcheckout_Split extends Mage_Core_Block_Template
{
	protected $salesrepTotals = array();

	public $orderNumber = 0;

	protected $_quote;
	protected $_checkout;

	/**
	 * Retrieve checkout session model
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	public function getCheckout()
	{
		if (empty($this->_checkout)) {
			$this->_checkout = Mage::getSingleton('checkout/session');
		}
		return $this->_checkout;
	}

	/**
	 * Retrieve sales quote model
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote()
	{
		if (empty($this->_quote)) {
			$this->_quote = $this->getCheckout()->getQuote();
		}
		return $this->_quote;
	}

	public function _getDefaultShippingMethod()
	{
		return $this->getCheckout()->getDefaultShippingMethod();
	}

	public function differentShippingAvailable()
	{
		return $this->getCheckout()->getDifferentShippingAvailable();
	}

	public function isCustomerLoggedIn()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn();
	}
}