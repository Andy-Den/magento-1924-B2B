<?php

class Idev_OneStepCheckout_Block_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{
	/**
	 * Retrieve available payment methods
	 *
	 * @return array
	 */

	public function getMethods()
	{
		$methods = $this->getData('methods');
		if ($methods === null) {
			$quote = $this->getQuote();
			$store = $quote ? $quote->getStoreId() : null;
			$methods = array();
			$errorMethods = array();
			foreach ($this->helper('payment')->getStoreMethods($store, $quote) as $method) {

				if (/*$this->_canUseMethod($method) &&*/ $method->isApplicableToQuote(
						$quote,
						Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL
					)) {
					$this->_assignMethod($method);
					$methods[] = $method;
				} elseif (!$method->isApplicableToQuote($this->getQuote(), Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY)) {
					$errorMethods[] = $this->__("You need to complete your data to use the '%s'.", $method->getTitle());
				}
			}
			$this->setData('methods', $methods);

			$this->setData('error_methods', $errorMethods);
		}
		return $methods;
	}
}