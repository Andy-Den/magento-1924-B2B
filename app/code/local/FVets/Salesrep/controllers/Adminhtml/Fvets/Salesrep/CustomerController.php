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
class FVets_Salesrep_Adminhtml_Fvets_Salesrep_CustomerController extends Mage_Adminhtml_CustomerController
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
        $this->setUsedModuleName('FVets_Salesrep');
    }

    /**
     * conditions in the customer page
     *
     * @access public
     * @return void
     */
    public function integrarepAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.edit.tab.integrarep');
        $this->renderLayout();
    }
}
