<?php
/**
 * Innoexts
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Admin html observer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Adminhtml_Observer
{
    /**
     * Get warehouse helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Add grid column
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function addGridColumn(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!$block) {
            return $this;
        }
        $adminhtmlHelper = $this->getWarehouseHelper()->getAdminhtmlHelper();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {
            $adminhtmlHelper->addQtyProductGridColumn($block);
            $adminhtmlHelper->addBatchPriceProductGridColumn($block);
        } else if ($block instanceof Mage_Adminhtml_Block_Report_Product_Lowstock_Grid) {
            $adminhtmlHelper->addQtyProductLowstockGridColumns($block);
        }
        return $this;
    }
    /**
     * Prepare grid
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function prepareGrid(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!$block) {
            return $this;
        }
        $adminhtmlHelper = $this->getWarehouseHelper()->getAdminhtmlHelper();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {
            $adminhtmlHelper->prepareProductGrid($block);
        } else if ($block instanceof Mage_Adminhtml_Block_Report_Product_Lowstock_Grid) {
            $adminhtmlHelper->prepareProductLowstockGrid($block);
        }
        return $this;
    }
    /**
     * Before load product collection
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function beforeLoadProductCollection($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if (!$collection) {
            return $this;
        }
        $adminhtmlHelper = $this->getWarehouseHelper()->getAdminhtmlHelper();
        if ($collection instanceof Mage_Reports_Model_Resource_Product_Lowstock_Collection) {
            $adminhtmlHelper->beforeLoadProductLowstockCollection($collection);
        }
        return $this;
    }
    /**
     * Process order create data
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function processOrderCreateData($observer)
    {
        $event              = $observer->getEvent();
        $orderCreateModel   = $event->getOrderCreateModel();
        $request            = $event->getRequest();
        if (!$orderCreateModel || !$request) {
            return $this;
        }
        if (isset($request['reset_items']) && $request['reset_items']) {
            $orderCreateModel->resetQuoteItems();
        }
        return $this;
    }
}