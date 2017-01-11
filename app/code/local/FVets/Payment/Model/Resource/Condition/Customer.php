<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Condition - customer relation model
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Model_Resource_Condition_Customer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     */
    protected function  _construct()
    {
        $this->_init('fvets_payment/condition_customer', 'rel_id');
    }
    /**
     * Save condition - customer relations
     *
     * @access public
     * @param FVets_Payment_Model_Condition $condition
     * @param array $data
     * @return FVets_Payment_Model_Resource_Condition_Customer
     */
    public function saveConditionRelation($condition, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('condition_id=?', $condition->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $customerId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'condition_id' => $condition->getId(),
                    'customer_id'    => $customerId,
                    'position'      => (isset($info['position'])) ? $info['position'] : 0
                )
            );
        }
        return $this;
    }

    /**
     * Save  customer - condition relations
     *
     * @access public
     * @param Mage_Customer_Model_Customer $customer
     * @param array $data
     * @return FVets_Payment_Model_Resource_Condition_Customer
     */
    public function saveCustomerRelation($customer, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('customer_id=?', $customer->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $conditionId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'condition_id' => $conditionId,
                    'customer_id'    => $customer->getId(),
                    'position'      => (isset($info['position'])) ? $info['position'] : 0
                )
            );
        }
        return $this;
    }
}
