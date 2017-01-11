<?php

class FVets_SalesRule_Model_SalesRule_Resource_Rule_Collection extends Mage_SalesRule_Model_Resource_Rule_Collection
{
	/**
	 * Filter collection by website(s), customer group(s) and date.
	 * Filter collection to only active rules.
	 * Sorting is not involved
	 *
	 * @param int $websiteId
	 * @param int $customerGroupId
	 * @param string|null $now
	 * @use $this->addWebsiteFilter()
	 *
	 * @return Mage_SalesRule_Model_Mysql4_Rule_Collection
	 */
	public function addWebsiteGroupDateFilter($websiteId, $customerGroupId, $now = null)
	{
		if (!$this->getFlag('website_group_date_filter')) {
			if (is_null($now)) {
				$now = Mage::getModel('core/date')->date('Y-m-d');
			}

			$this->addWebsiteFilter($websiteId);

			$customer = $this->getCustomer();

			$entityInfo = $this->_getAssociatedEntityInfo('customer_group');
			$connection = $this->getConnection();
			$this->getSelect()
				->joinLeft(
					array('customer_group_ids' => $this->getTable($entityInfo['associations_table'])),
						'main_table.' . $entityInfo['rule_id_field'] . ' = customer_group_ids.' . $entityInfo['rule_id_field'],
					array()
				)
				->joinLeft(
					array('related_customer' => $this->getTable('fvets_salesrule/customer')),
					'main_table.' . $entityInfo['rule_id_field'] . ' = related_customer.salesrule_id',
					array()
				)
				->where(new Zend_Db_Expr(
						"main_table.apply_to_all = 1 OR " .
						$connection->quoteInto("customer_group_ids." . $entityInfo['entity_id_field'] . ' = ?',  (int)$customerGroupId) . ' OR ' .
						'related_customer.customer_id = ' . $customer->getId()
					)
				)
				->where('from_date is null or from_date <= ?', $now)
				->where('to_date is null or to_date >= ?', $now)
				->group('main_table.rule_id')
			;


			$this->addIsActiveFilter();

			$this->setFlag('website_group_date_filter', true);
		}

		return $this;
	}

	public function addCustomerFilter($customer = null)
	{
		if (!isset($customer))
		{
			$customer = $this->getCustomer();
		}

		if ($customer->getId()) {
			$constraint = 'related.customer_id='.$customer->getId();
		} else {
			$constraint = 'related.customer_id=0';
		}
		$this->getSelect()->join(
			array('related' => $this->getTable('fvets_salesrule/customer')),
			'related.salesrule_id=main_table.rule_id AND '.$constraint,
			array('position')
		);
		return $this;
	}

	/**
	 * get the current customer
	 *
	 * @access public
	 * @return Mage_Catalog_Model_Customer
	 * @author Douglas Borella Ianitsky
	 */
	public function getCustomer()
	{
		if (!$customer = Mage::registry('current_customer'))
		{
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		return $customer;
	}
}