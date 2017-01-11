<?php

class FVets_SalesRule_Model_Adminhtml_Premier_Observer
{
	function excludeDuplicatedCustomerPromo($observer)
	{
		$customer	= $observer->getCustomer();
		$rule		= $observer->getRule();

		if (!$customer instanceof Mage_Customer_Model_Customer)
		{
			$customer = Mage::getModel('customer/customer')->load($customer);
		}

		if (!$rule instanceof Mage_SalesRule_Model_Rule)
		{
			$rule = Mage::getModel('salesrule/rule')->load($rule);
		}

		if ($rule->getRuleType() == '2')
		{
			$premier = Mage::getModel('fvets_salesrule/salesrule_premier')
				->getCollection()
				->addFieldToFilter('salesrule_id', $rule->getId())
			;
			$premier = $premier->getFirstItem();

			$collection = Mage::getModel('fvets_salesrule/salesrule_customer')
				->getCollection()
				->joinFields()
			;
			$collection->getSelect()
				->joinInner(
					array('premier' => 'fvets_salesrule_premier'),
					'premier.salesrule_id = related.salesrule_id AND premier.group = "' . $premier->getGroup() . '"',
					array()
				)
				->joinInner(
					array('rule' => 'salesrule'),
					'rule.rule_id = related.salesrule_id AND rule.rule_id <> ' . $rule->getId() . ' AND rule.rule_type = 2',
					array('rule_id')
				)
			;

			foreach ($collection as $customer)
			{
				$customer = Mage::getModel('fvets_salesrule/salesrule_customer')->load($customer->getRelId())->delete();
			}
		}
	}
}