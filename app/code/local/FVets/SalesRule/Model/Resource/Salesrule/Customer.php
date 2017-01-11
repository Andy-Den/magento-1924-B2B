<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule - customer relation model
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Model_Resource_Salesrule_Customer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Douglas Borella Ianitsky
     */
    protected function  _construct()
    {
        $this->_init('fvets_salesrule/customer', 'rel_id');
    }
    /**
     * Save salesrule - customer relations
     *
     * @access public
     * @param FVets_SalesRule_Model_Salesrule $salesrule
     * @param array $data
     * @return FVets_SalesRule_Model_Resource_Salesrule_Customer
     * @author Douglas Borella Ianitsky
     */
    public function saveSalesruleRelation($salesrule, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('salesrule_id=?', $salesrule->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $customerId => $info) {
            $insert = $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'salesrule_id' => $salesrule->getId(),
                    'customer_id'    => $customerId,
                    'position'      => (isset($info['position'])) ? $info['position'] : 0,
                )
            );

			if ($insert)
			{




				Mage::dispatchEvent('fvets_salesrule_save_customer_relation', array('rule' => $salesrule, 'customer' => $customerId));
			}

        }
        return $this;
    }

    /**
     * Save  customer - salesrule relations
     *
     * @access public
     * @param Mage_Customer_Model_Customer $customer
     * @param array $data
     * @return FVets_SalesRule_Model_Resource_Salesrule_Customer
     * @@author Douglas Borella Ianitsky
     */
    public function saveCustomerRelation($customer, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('customer_id=?', $customer->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $salesruleId => $info) {
            $insert = $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'salesrule_id' => $salesruleId,
                    'customer_id'    => $customer->getId(),
                    'position'      => (@$info['position']) ? $info['position'] : 0,
                )
            );

			if ($insert)
			{
				Mage::dispatchEvent('fvets_salesrule_save_customer_relation', array('rule' => $salesruleId, 'customer' => $customer));
			}
        }
        return $this;
    }

	public function deletePremierCustomerRelation($customer)
	{
		$collection = Mage::getModel('salesrule/rule')
			->getCollection()
			->addFieldToFilter('rule_type', 2)
		;

		$deleteCondition1 = $this->_getWriteAdapter()->quoteInto('customer_id=?', $customer->getId());
		$deleteCondition2 = $this->_getWriteAdapter()->quoteInto('salesrule_id IN (?)', implode(',', $collection->getAllIds()));
		$this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition1 . " AND " . $deleteCondition2);
	}

	public function deleteAllPremierCustomersRelation()
	{
		$rules = Mage::getModel('salesrule/rule')
			->getCollection()
			->addFieldToFilter('rule_type', 2)
		;

		foreach ($rules  as $rule)
		{
			$deleteCondition = $this->_getWriteAdapter()->quoteInto('salesrule_id = ?', $rule->getId());
			$this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);
		}
	}

	public function deletePremierCustomersByCustomerAndRuleRelation($rule, $customer)
	{
		$deleteConditionRule = $this->_getWriteAdapter()->quoteInto('salesrule_id = ?', $rule->getId());
		$deleteConditionCustomer = $this->_getWriteAdapter()->quoteInto('customer_id = ?', $customer->getId());
		$this->_getWriteAdapter()->delete($this->getMainTable(), $deleteConditionRule . ' AND ' . $deleteConditionCustomer);
	}

	/**
	 * Save premier salesrule - customer relations
	 *
	 * @access public
	 * @param FVets_SalesRule_Model_Salesrule $salesrule
	 * @param array $data
	 * @return FVets_SalesRule_Model_Resource_Salesrule_Customer
	 * @author Douglas Borella Ianitsky
	 */
	public function savePremierSalesruleRelation($salesrule, $data)
	{
		if (!is_array($data)) {
			$data = array();
		}

		foreach ($data as $customerId => $info) {
			$insert = $this->_getWriteAdapter()->insert(
				$this->getMainTable(),
				array(
					'salesrule_id' => $salesrule->getId(),
					'customer_id'    => $customerId,
					'position'      => @$info['position']
				)
			);

			if ($insert)
			{
				Mage::dispatchEvent('fvets_salesrule_save_customer_relation', array('rule' => $salesrule, 'customer' => $customerId));
			}
		}
		return $this;
	}
}
