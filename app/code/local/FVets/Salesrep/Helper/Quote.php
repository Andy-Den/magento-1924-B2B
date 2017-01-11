<?php

/**
 * Salesrep default helper
 *
 * @category    FVets
 * @package     FVets_Salesrep
 */
class FVets_Salesrep_Helper_Quote extends Mage_Core_Helper_Abstract
{
	private $_checkout_session = null;

	public function getSalesrepItems($items, $force = false)
	{
		if ($this->getQuote()->getSplitBySalesrep())
		{
			foreach ($items as $key => $item)
			{
				if ($force || !$item->getSalesrepId())
				{
					$salesrep = Mage::helper('fvets_salesrep')->getSalesrepByCustomerAndProduct(Mage::getSingleton('customer/session')->getCustomer(), $item->getProduct());
					if ($salesrep->getId())
					{
						$item->setSalesrepId($salesrep->getId());
					}
				}

				if ($item->getSalesrepId() != $this->getQuote()->getSalesrep()->getId())
				{
					unset($items[$key]);
				}
				else
				{
					$items[$key] = $item;
				}
			}
		}
		return $items;
	}

	public function removeQuoteUnavailableSalesrepItems($items, $force = false)
	{
		if ($this->getQuote()->getSplitBySalesrep())
		{
			foreach ($items as $key => $item)
			{
				if ($force || !$item->getSalesrepId())
				{
					$salesrep = Mage::helper('fvets_salesrep')->getSalesrepByCustomerAndProduct(Mage::getSingleton('customer/session')->getCustomer(), $item->getProduct());
					if ($salesrep->getId())
					{
						$item->setSalesrepId($salesrep->getId());
					}
				}

				if ($item->getSalesrepId() != $this->getQuote()->getSalesrep()->getId())
				{
					unset($items[$key]);
				}
				else
				{
					$items[$key] = $item;
				}
			}
		}
		return $items;
	}

	/**
	 * Retrieve checkout session model
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	public function getCheckoutSession()
	{
		if (is_null($this->_checkout_session)) {
			$this->_checkout_session = Mage::getSingleton('checkout/session');
		}
		return $this->_checkout_session;
	}

	/**
	 * Retrieve quote model
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote()
	{
		return $this->getCheckoutSession()->getQuote();
	}
}