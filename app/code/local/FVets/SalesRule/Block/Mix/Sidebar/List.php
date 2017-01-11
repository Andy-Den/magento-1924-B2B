<?php

class FVets_SalesRule_Block_Mix_Sidebar_List extends Mage_Core_Block_Template
{
	public function getPromosMix()
	{
		return false;
		if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$collection = Mage::getModel('salesrule/rule')->getCollection()
				->setValidationFilter(Mage::app()->getWebsite()->getId(), Mage::getSingleton('customer/session')->getCustomer()->getGroupId())
				->addFieldToFilter('is_mix', 1)
				->setPageSize(10)
				->setCurPage(1);
			;
			return $collection;
		}
		else
		{
			return false;
		}
	}
}