<?php

class FVets_SalesRule_Helper_Report extends FVets_SalesRule_Helper_Data
{
	public function saveReport($rule, $customer, $modified_by, $salesrep_id = null)
	{
		if (!$customer instanceof Mage_Customer_Model_Customer)
		{
			$customer = Mage::getModel('customer/customer')->load($customer);
		}

		if (!$rule instanceof Mage_SalesRule_Model_Rule)
		{
			$rule = Mage::getModel('salesrule/rule')->load($rule);
		}

		$premier = Mage::getModel('fvets_salesrule/salesrule_premier')
			->getCollection()
			->addFieldToFilter('salesrule_id', $rule->getId())
		;
		$premier = $premier->getFirstItem();

		$report = Mage::getModel('fvets_salesrule/salesrule_premier_report')->getCollection()
			->addFieldToFilter('customer_id', $customer->getId())
		;
		$report->getSelect()
			->order('report_id DESC')
			->limit('1')
		;

		$report = $report->getFirstItem();

		if ($report->getRuleId() != $rule->getId())
		{
			$report = Mage::getModel('fvets_salesrule/salesrule_premier_report')
				->setSalesruleId($rule->getId())
				->setCustomerId($customer->getId())
				->setGroup($premier->getGroup())
				->setFrom($premier->getFrom())
				->setTo($premier->getTo())
				->setModifiedBy($modified_by)
				->setSalesrepId($salesrep_id)
				->save()
			;
		}
	}
}