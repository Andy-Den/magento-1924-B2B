<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * Customer helper
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Helper_Customer extends FVets_SalesRule_Helper_Data
{

    /**
     * get the selected salesrules for a customer
     *
     * @access public
     * @param Mage_Customer_Model_Customer $customer
     * @return array()
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedSalesrules(Mage_Customer_Model_Customer $customer)
    {
        if (!$customer->hasSelectedSalesrules()) {
            $salesrules = array();
            foreach ($this->getSelectedSalesrulesCollection($customer) as $salesrule) {
                $salesrules[] = $salesrule;
            }
            $customer->setSelectedSalesrules($salesrules);
        }
        return $customer->getData('selected_salesrules');
    }

    /**
     * get salesrule collection for a customer
     *
     * @access public
     * @param Mage_Customer_Model_Customer $customer
     * @return FVets_SalesRule_Model_Resource_Salesrule_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedSalesrulesCollection(Mage_Customer_Model_Customer $customer)
    {
        $collection = Mage::getResourceSingleton('salesrule/rule_collection')
            ->addCustomerFilter($customer);
        return $collection;
    }
}
