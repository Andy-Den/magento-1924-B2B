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
 * Condition category model
 *
 * @category    FVets
 * @package     FVets_Payment
 * @author      Douglas Borella Ianitsky
 */
class FVets_Payment_Model_Condition_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Douglas Borella Ianitsky
     */
    protected function _construct()
    {
        $this->_init('fvets_payment/condition_category');
    }

    /**
     * Save data for condition-category relation
     *
     * @access public
     * @param  FVets_Payment_Model_Condition $condition
     * @return FVets_Payment_Model_Condition_Category
     * @author Douglas Borella Ianitsky
     */
    public function saveConditionRelation($condition)
    {
        $data = $condition->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveConditionRelation($condition, $data);
        }
        return $this;
    }

    /**
     * get categories for condition
     *
     * @access public
     * @param FVets_Payment_Model_Condition $condition
     * @return FVets_Payment_Model_Resource_Condition_Category_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getCategoryCollection($condition)
    {
        $collection = Mage::getResourceModel('fvets_payment/condition_category_collection')
            ->addConditionFilter($condition);
        return $collection;
    }
}
