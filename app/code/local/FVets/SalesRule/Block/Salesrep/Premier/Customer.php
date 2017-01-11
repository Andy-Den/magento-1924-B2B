<?php

class FVets_SalesRule_Block_Salesrep_Premier_Customer extends Mage_Page_Block_Html
{
	private $_customer = null;

	public function getCustomer()
	{
		if (!isset($this->_customer))
		{
			$this->_customer = Mage::getModel('customer/customer')->load(Mage::app()->getRequest()->getParam('customer_id'));
		}
		return $this->_customer;
	}

	public function getSalesruleCollection()
	{
		$collection = Mage::getModel('salesrule/rule')->getCollection()
			//->addFieldToFilter('rule_type', '2')
		;

		if ($this->getCustomer()->getId()) {
			$constraint = 'website.website_id='.$this->getCustomer()->getWebsiteId();
		} else {
			$constraint = 'website.website_id=0';
		}
		$collection->getSelect()->join(
				array('website' => $collection->getTable('salesrule/website')),
				'website.rule_id=main_table.rule_id AND '.$constraint,
				array('website_id')
			)->join(
				array('premier' => $collection->getTable('fvets_salesrule/premier')),
				'premier.salesrule_id=main_table.rule_id',
				array('from', 'to', 'calculation_type', 'group')
			)
		;

		$collection->getSelect()->order('premier.group');
		$collection->getSelect()->order('main_table.sort_order DESC');


		return $collection;
	}

	public function getSelectedSalesruleCollection()
	{
		$collection = Mage::getResourceSingleton('salesrule/rule_collection');
		$collection->addCustomerFilter($this->getCustomer());
		return $collection;
	}
}