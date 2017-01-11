<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group - product controller
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
require_once ("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class FVets_CatalogRestrictionGroup_Adminhtml_Catalogrestrictiongroup_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
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
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.catalogrestrictiongroup')
            ->setProductCatalogrestrictiongroups($this->getRequest()->getPost('product_catalogrestrictiongroups', null));
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
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.catalogrestrictiongroup')
            ->setProductCatalogrestrictiongroups($this->getRequest()->getPost('product_catalogrestrictiongroups', null));
        $this->renderLayout();
    }
}
