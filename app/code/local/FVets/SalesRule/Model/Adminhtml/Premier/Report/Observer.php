<?php

class FVets_SalesRule_Model_Adminhtml_Premier_Report_Observer
{
	function saveReport($observer)
	{
		Mage::helper('fvets_salesrule/report')->saveReport($observer->getRule(), $observer->getCustomer(), 'system');
	}
}