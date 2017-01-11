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
 * Condition - customer controller
 * @category    FVets
 * @package     FVets_Payment
 */
require_once ("Mage/Adminhtml/controllers/CustomerController.php");
class FVets_Payment_Adminhtml_Payment_Condition_CustomerController extends Mage_Adminhtml_CustomerController
{
    /**
     * construct
     *
     * @access protected
     * @return void
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('FVets_Payment');
    }

    /**
     * conditions in the customer page
     *
     * @access public
     * @return void
     */
    public function conditionsAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.edit.tab.condition')
            ->setCustomerConditions($this->getRequest()->getPost('customer_conditions', null));
        $this->renderLayout();
    }

    /**
     * conditions grid in the customer page
     *
     * @access public
     * @return void
     */
    public function conditionsGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.edit.tab.condition')
            ->setCustomerConditions($this->getRequest()->getPost('customer_conditions', null));
        $this->renderLayout();
    }
}
