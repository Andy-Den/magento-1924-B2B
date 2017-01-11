<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule admin controller
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
include('Mage/Adminhtml/controllers/Promo/QuoteController.php');

class FVets_SalesRule_Adminhtml_SalesruleController extends Mage_Adminhtml_Promo_QuoteController
{
    /**
     * init the salesrule
     *
     * @access protected
     * @return FVets_SalesRule_Model_Salesrule
     */
    protected function _initSalesrule()
    {
		parent::_initRule();
		return Mage::registry('current_promo_quote_rule');
    }

    /**
     * get grid of customers action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function customersAction()
    {
        $this->_initRule();
        $this->loadLayout();
        $this->getLayout()->getBlock('salesrule.edit.tab.customer')
            ->setSalesruleCustomers($this->getRequest()->getPost('salesrule_customers', null));
        $this->renderLayout();
    }

    /**
     * get grid of customers action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function customersgridAction()
    {
        $this->_initSalesrule();
        $this->loadLayout();
        $this->getLayout()->getBlock('salesrule.edit.tab.customer')
            ->setSalesruleCustomers($this->getRequest()->getPost('salesrule_customers', null));
        $this->renderLayout();
    }
}
