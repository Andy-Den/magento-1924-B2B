<?php

class FVets_SalesRule_Block_Salesrep_Premier extends Mage_Core_Block_Template
{
	function getSalesrepCustomers()
	{
		return Mage::helper('fvets_salesrep')->getSalesRepCustomers(Mage::getSingleton('customer/session')->getCustomer()->getFvetsSalesrep());
	}
}
