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
 * Condition - customer relation resource model collection
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Model_Resource_Condition_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return FVets_Payment_Model_Resource_Condition_Customer_Collection
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('fvets_payment/condition_customer')),
                'related.customer_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add condition filter
     *
     * @access public
     * @param FVets_Payment_Model_Condition | int $condition
     * @return FVets_Payment_Model_Resource_Condition_Customer_Collection
     */
    public function addConditionFilter($condition)
    {
        if ($condition instanceof FVets_Payment_Model_Condition) {
            $condition = $condition->getId();
        }
        if (!$this->_joinedFields ) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.condition_id = ?', $condition);
        return $this;
    }
}
