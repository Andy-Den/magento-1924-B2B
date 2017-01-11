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
 * Process helper
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Helper_Index_Process 
    extends Mage_Core_Helper_Abstract 
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
     * Get product attribute process
     * 
     * @return Mage_Index_Model_Process
     */
    protected function getProductAttribute()
    {
        return Mage::getSingleton('index/indexer')
            ->getProcessByCode('catalog_product_attribute');
    }
    /**
     * Get product price process
     * 
     * @return Mage_Index_Model_Process
     */
    protected function getProductPrice()
    {
        return Mage::getSingleton('index/indexer')
            ->getProcessByCode('catalog_product_price');
    }
    /**
     * Get stock process 
     * 
     * @return Mage_Index_Model_Process
     */
    protected function getStock()
    {
        return Mage::getSingleton('index/indexer')
            ->getProcessByCode('cataloginventory_stock');
    }
    /**
     * Reindex product attribute
     * 
     * @return Innoexts_Warehouse_Helper_Index_Process
     */
    public function reindexProductAttribute()
    {
        $process = $this->getProductAttribute();
        if ($process) {
            $process->reindexAll();
        }
        return $this;
    }
    /**
     * Reindex product price
     * 
     * @return Innoexts_Warehouse_Helper_Index_Process
     */
    public function reindexProductPrice()
    {
        $process = $this->getProductPrice();
        if ($process) {
            $process->reindexAll();
        }
        return $this;
    }
    /**
     * Reindex stock
     * 
     * @return Innoexts_Warehouse_Helper_Index_Process
     */
    public function reindexStock()
    {
        $process = $this->getStock();
        if ($process) {
            $process->reindexAll();
        }
        return $this;
    }
    /**
     * Change product attribute process status
     * 
     * @param int $status
     * 
     * @return Innoexts_Warehouse_Helper_Index_Process
     */
    public function changeProductAttributeStatus($status)
    {
        $process = $this->getProductAttribute();
        if ($process) {
            $process->changeStatus($status);
        }
        return $this;
    }
    /**
     * Change product price process status
     * 
     * @param int $status
     * 
     * @return Innoexts_Warehouse_Helper_Index_Process
     */
    public function changeProductPriceStatus($status)
    {
        $process = $this->getProductPrice();
        if ($process) {
            $process->changeStatus($status);
        }
        return $this;
    }
    /**
     * Change stock process status
     * 
     * @param int $status
     * 
     * @return Innoexts_Warehouse_Helper_Index_Process
     */
    public function changeStockStatus($status)
    {
        $process = $this->getStock();
        if ($process) {
            $process->changeStatus($status);
        }
        return $this;
    }
}