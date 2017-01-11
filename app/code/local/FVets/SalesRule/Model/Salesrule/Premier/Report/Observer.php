<?php

class FVets_SalesRule_Model_Salesrule_Premier_Report_Observer
{
	function saveReport($observer)
	{
		Mage::helper('fvets_salesrule/report')->saveReport($observer->getRule(), $observer->getCustomer(), 'salesrep', Mage::getSingleton('customer/session')->getCustomer()->getFvetsSalesrep());
	}
}