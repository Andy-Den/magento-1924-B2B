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
 * Product price observer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Catalog_Product_Price_Observer 
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
     * Batch Price
     */
    
    /**
     * Save batch prices
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Catalog_Product_Price_Observer
     */
    public function saveBatchPrices(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceHelper()
            ->saveBatchPrices($observer->getEvent()->getProduct());
        return $this;
    }
    /**
     * Load batch prices
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Catalog_Product_Price_Observer
     */
    public function loadBatchPrices(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceHelper()
            ->loadBatchPrices($observer->getEvent()->getProduct());
        return $this;
    }
    /**
     * Load collection batch prices
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Catalog_Product_Price_Observer
     */
    public function loadCollectionBatchPrices(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceHelper()
            ->loadCollectionBatchPrices($observer->getEvent()->getCollection());
        return $this;
    }
    /**
     * Remove batch prices
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Catalog_Product_Price_Observer
     */
    public function removeBatchPrices(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceHelper()
            ->removeBatchPrices($observer->getEvent()->getProduct());
        return $this;
    }
    
    /**
     * Batch Special Price
     */
    
    /**
     * Save batch special prices
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Catalog_Product_Price_Observer
     */
    public function saveBatchSpecialPrices(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceHelper()
            ->saveBatchSpecialPrices($observer->getEvent()->getProduct());
        return $this;
    }
    /**
     * Load batch special prices
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Catalog_Product_Price_Observer
     */
    public function loadBatchSpecialPrices(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceHelper()
            ->loadBatchSpecialPrices($observer->getEvent()->getProduct());
        return $this;
    }
    /**
     * Load collection batch special prices
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Catalog_Product_Price_Observer
     */
    public function loadCollectionBatchSpecialPrices(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceHelper()
            ->loadCollectionBatchSpecialPrices($observer->getEvent()->getCollection());
        return $this;
    }
    /**
     * Remove batch special prices
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Catalog_Product_Price_Observer
     */
    public function removeBatchSpecialPrices(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceHelper()
            ->removeBatchSpecialPrices($observer->getEvent()->getProduct());
        return $this;
    }
    
    /**
     * Before collection load
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Observer
     */
    public function beforeCollectionLoad(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceIndexerHelper()
            ->addPriceIndexFilter($observer->getEvent()->getCollection());
        return $this;
    }
    /**
     * After collection apply limitations
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Observer
     */
    public function afterCollectionApplyLimitations(Varien_Event_Observer $observer)
    {
        $this->getWarehouseHelper()
            ->getProductPriceIndexerHelper()
            ->addPriceIndexFilter($observer->getEvent()->getCollection());
        return $this;
    }
}