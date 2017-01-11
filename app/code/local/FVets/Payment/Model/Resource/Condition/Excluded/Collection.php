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
 * Condition - Category relation resource model collection
 *
 * @category    FVets
 * @package     FVets_Payment
 * @author      Douglas Borella Ianitsky
 */
class FVets_Payment_Model_Resource_Condition_Excluded_Collection extends Mage_Catalog_Model_Resource_Category_Collection
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
     * @return FVets_Payment_Model_Resource_Condition_Excluded_Collection
     * @author Douglas Borella Ianitsky
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('fvets_payment/condition_excluded')),
                'related.category_id = e.entity_id',
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
     * @return FVets_Payment_Model_Resource_Condition_Excluded_Collection
     * @author Douglas Borella Ianitsky
     */
    public function addConditionFilter($condition)
    {
        if ($condition instanceof FVets_Payment_Model_Condition) {
            $condition = $condition->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.condition_id = ?', $condition);
        return $this;
    }
}
