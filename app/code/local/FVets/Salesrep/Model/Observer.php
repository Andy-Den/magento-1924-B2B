<?php

class FVets_Salesrep_Model_Observer
{

	private $allowedRoutes = array(
		//'customer',
		'sales',
		'fvets_salesrule',
		'salesrule',
		//'review',
		//'newsletter'
	);


	public function checkSalesrepActionAllowed()
	{
		$customerSession = Mage::getSingleton('customer/session');
		if ($customerSession->isLoggedIn())
		{
			if (Mage::helper('fvets_salesrep')->isLoggedUserRep())
			{
				$route = strtolower(Mage::app()->getRequest()->getRouteName());
				if (!in_array($route, $this->allowedRoutes))
				{
					Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('sales/order/history'));
				}
			}
		}
	}

	public function customerSalesrepLogin()
	{
		if (Mage::helper('fvets_salesrep')->isLoggedUserRep())
		{
			Mage::getSingleton('fvets_salesrep/session')->setCustomer(Mage::getSingleton('customer/session')->getCustomer());
		}
	}

	public function customerSalesrepLogout()
	{
		Mage::getSingleton('fvets_salesrep/session')->clear();
	}

	/**
	 * Adds the product salesrep when an item was added to the order
	 * @param $observer | Varien_Event_Observer
	 */
	public function setOrderItemSalerep($observer)
	{
		$item = $observer->getOrderItem();
		if (!$item->getSalesrepId())
		{
			$salesrep = Mage::helper('fvets_salesrep')->getSalesrepByCustomerAndProduct(Mage::getSingleton('customer/session')->getCustomer(), $item->getProduct());
			if ($salesrep->getId())
			{
				$item->setSalesrepId($salesrep->getId());
			}
		}
	}

	/**
	 * Adds the product salesrep when an item was added to the quote
	 * @param $observer | Varien_Event_Observer
	 */
	public function setQuoteItemSalerep($observer)
	{
		$item = $observer->getQuoteItem();
		if (!$item->getSalesrepId())
		{
			$salesrep = Mage::helper('fvets_salesrep')->getSalesrepByCustomerAndProduct(Mage::getSingleton('customer/session')->getCustomer(), $item->getProduct());
			if ($salesrep->getId())
			{
				$item->setSalesrepId($salesrep->getId());
			}
		}
	}
}