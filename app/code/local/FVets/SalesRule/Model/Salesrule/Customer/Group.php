<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule customer model
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Model_Salesrule_Customer_Group extends Mage_Core_Model_Abstract
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
        $this->_init('fvets_salesrule/salesrule_customer_group');
    }
}
