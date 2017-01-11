<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule - customer controller
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
require_once ("Mage/Adminhtml/controllers/CustomerController.php");
class FVets_SalesRule_Adminhtml_Salesrule_CustomerController extends Mage_Adminhtml_CustomerController
{
    /**
     * construct
     *
     * @access protected
     * @return void
     * @author Douglas Borella Ianitsky
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('FVets_SalesRule');
    }

    /**
     * salesrules in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function salesrulesAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.edit.tab.salesrule.promo')
            ->setCustomerSalesrules($this->getRequest()->getPost('customer_salesrules', null));
        $this->renderLayout();
    }

    /**
     * salesrules grid in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function salesrulesGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.edit.tab.salesrule.promo')
            ->setCustomerSalesrules($this->getRequest()->getPost('customer_salesrules', null));
        $this->renderLayout();
    }
}
