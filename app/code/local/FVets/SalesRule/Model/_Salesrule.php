<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule model
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Model_Salesrule extends Mage_SalesRule_Model_Rule
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'fvets_salesrule_salesrule';
    const CACHE_TAG = 'fvets_salesrule_salesrule';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_customerInstance = null;

    /**
     * save salesrule relation
     *
     * @access public
     * @return FVets_SalesRule_Model_Salesrule
     * @author Douglas Borella Ianitsky
     */
    protected function _afterSave()
    {
        $this->getCustomerInstance()->saveSalesruleRelation($this);
        return parent::_afterSave();
    }

    /**
     * get customer relation model
     *
     * @access public
     * @return FVets_SalesRule_Model_Salesrule_Customer
     * @author Douglas Borella Ianitsky
     */
    public function getCustomerInstance()
    {
        if (!$this->_customerInstance) {
            $this->_customerInstance = Mage::getSingleton('fvets_salesrule/salesrule_customer');
        }
        return $this->_customerInstance;
    }

    /**
     * get selected customers array
     *
     * @access public
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedCustomers()
    {
        if (!$this->hasSelectedCustomers()) {
            $customers = array();
            foreach ($this->getSelectedCustomersCollection() as $customer) {
                $customers[] = $customer;
            }
            $this->setSelectedCustomers($customers);
        }
        return $this->getData('selected_customers');
    }

    /**
     * Retrieve collection selected customers
     *
     * @access public
     * @return FVets_SalesRule_Resource_Salesrule_Customer_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedCustomersCollection()
    {
        $collection = $this->getCustomerInstance()->getCustomerCollection($this);
        return $collection;
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        $values['rule_type'] = '1';

        return $values;
    }
    
}
