<?php
/**
 * Classic_Distributor extension
 * 
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Distributor - region controller
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
require_once ("Mage/Adminhtml/controllers/Catalog/RegionController.php");
class Classic_Distributor_Adminhtml_Distributor_Distributor_Catalog_RegionController extends Mage_Adminhtml_Catalog_RegionController
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
        $this->setUsedModuleName('Classic_Distributor');
    }

    /**
     * distributors in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function distributorsAction()
    {
        $this->_initRegion();
        $this->loadLayout();
        $this->getLayout()->getBlock('region.edit.tab.distributor')
            ->setRegionDistributors($this->getRequest()->getPost('region_distributors', null));
        $this->renderLayout();
    }

    /**
     * distributors grid in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function distributorsGridAction()
    {
        $this->_initRegion();
        $this->loadLayout();
        $this->getLayout()->getBlock('region.edit.tab.distributor')
            ->setRegionDistributors($this->getRequest()->getPost('region_distributors', null));
        $this->renderLayout();
    }
}
