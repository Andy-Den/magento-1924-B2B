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
class FVets_SalesRule_Model_Salesrule_Customer extends Mage_Core_Model_Abstract
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
        $this->_init('fvets_salesrule/salesrule_customer');
    }

    /**
     * Save data for salesrule-customer relation
     * @access public
     * @param  FVets_SalesRule_Model_Salesrule $salesrule
     * @return FVets_SalesRule_Model_Salesrule_Customer
     * @author Douglas Borella Ianitsky
     */
    public function saveSalesruleRelation($salesrule)
    {
        $data = $salesrule->getCustomersData();
        if (!is_null($data)) {
            $this->_getResource()->saveSalesruleRelation($salesrule, $data);
        }
        return $this;
    }

    /**
     * get customers for salesrule
     *
     * @access public
     * @param FVets_SalesRule_Model_Salesrule $salesrule
     * @return FVets_SalesRule_Model_Resource_Salesrule_Customer_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getCustomerCollection($salesrule)
    {
        $collection = Mage::getResourceModel('fvets_salesrule/salesrule_customer_collection')
            ->addSalesruleFilter($salesrule);
        return $collection;
    }


}
