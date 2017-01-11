<?php

class FVets_CheckoutSplit_Model_Observer
{
	public function setOrderSalesrep($observer)
	{
		if (Mage::getSingleton('checkout/session')->getQuote()->getSalesrep())
		{
			$order = $observer->getOrder();
			$order->setSalesrepId(Mage::getSingleton('checkout/session')->getQuote()->getSalesrep()->getId());
		}
		//$quote = $observer->getQuote();

		//$order->setSalesrepId($quote->getSalesrep()->getId());
		//$order->save();
	}
}