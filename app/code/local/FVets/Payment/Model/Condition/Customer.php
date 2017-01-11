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
 * Condition customer model
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Model_Condition_Customer extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     */
    protected function _construct()
    {
        $this->_init('fvets_payment/condition_customer');
    }

    /**
     * Save data for condition-customer relation
     * @access public
     * @param  FVets_Payment_Model_Condition $condition
     * @return FVets_Payment_Model_Condition_Customer
     */
    public function saveConditionRelation($condition)
    {
        $data = $condition->getCustomersData();
        if (!is_null($data)) {
            $this->_getResource()->saveConditionRelation($condition, $data);
        }
        return $this;
    }

    /**
     * get customers for condition
     *
     * @access public
     * @param FVets_Payment_Model_Condition $condition
     * @return FVets_Payment_Model_Resource_Condition_Customer_Collection
     */
    public function getCustomerCollection($condition)
    {
        $collection = Mage::getResourceModel('fvets_payment/condition_customer_collection')
            ->addConditionFilter($condition);
        return $collection;
    }
}
