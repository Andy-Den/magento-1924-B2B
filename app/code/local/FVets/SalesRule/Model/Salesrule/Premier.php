<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule premier model
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Model_Salesrule_Premier extends Mage_Core_Model_Abstract
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
        $this->_init('fvets_salesrule/salesrule_premier');
    }

    /**
     * get premier rule for salesrule
     *
     * @access public
     * @param FVets_SalesRule_Model_Salesrule $salesrule
     * @return FVets_SalesRule_Model_Resource_Salesrule_Premier_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getPremierCollection($salesrule)
    {
        $collection = Mage::getResourceModel('fvets_salesrule/salesrule_premier_collection')
            ->addSalesruleFilter($salesrule);
        return $collection;
    }
}
