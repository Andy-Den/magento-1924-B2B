<?php
/**
 * FVets_Salesrep extension
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 */
/**
 * Salesrep - region controller
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
require_once ("Mage/Adminhtml/controllers/Catalog/RegionController.php");
class FVets_Salesrep_Adminhtml_FVets_Salesrep_Catalog_RegionController extends Mage_Adminhtml_Catalog_RegionController
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
        $this->setUsedModuleName('FVets_Salesrep');
    }

    /**
     * salesreps in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function salesrepsAction()
    {
        $this->_initRegion();
        $this->loadLayout();
        $this->getLayout()->getBlock('region.edit.tab.salesrep')
            ->setRegionSalesreps($this->getRequest()->getPost('region_salesreps', null));
        $this->renderLayout();
    }

    /**
     * salesreps grid in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function salesrepsGridAction()
    {
        $this->_initRegion();
        $this->loadLayout();
        $this->getLayout()->getBlock('region.edit.tab.salesrep')
            ->setRegionSalesreps($this->getRequest()->getPost('region_salesreps', null));
        $this->renderLayout();
    }
}
