<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group - customer controller
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
require_once ("Mage/Adminhtml/controllers/CustomerController.php");
class FVets_CatalogRestrictionGroup_Adminhtml_Catalogrestrictiongroup_CustomerController extends Mage_Adminhtml_CustomerController
{
    /**
     * construct
     *
     * @access protected
     * @return void
     * @author Douglas Ianitsky
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('FVets_CatalogRestrictionGroup');
    }

    /**
     * restriction groups in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function catalogrestrictiongroupsAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.edit.tab.catalogrestrictiongroup')
            ->setCustomerCatalogrestrictiongroups($this->getRequest()->getPost('customer_catalogrestrictiongroups', null));
        $this->renderLayout();
    }

    /**
     * restriction groups grid in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function catalogrestrictiongroupsGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('customer.edit.tab.catalogrestrictiongroup')
            ->setCustomerCatalogrestrictiongroups($this->getRequest()->getPost('customer_catalogrestrictiongroups', null));
        $this->renderLayout();
    }
}
