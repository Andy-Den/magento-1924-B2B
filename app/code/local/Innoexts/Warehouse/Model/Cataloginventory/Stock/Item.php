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
 * Stock item
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Cataloginventory_Stock_Item 
    extends Mage_CatalogInventory_Model_Stock_Item 
{
    /**
     * Get helper
     * 
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get catalog inventory helper
     * 
     * @return Innoexts_Warehouse_Helper_Cataloginventory
     */
    protected function getCatalogInventoryHelper()
    {
        return $this->getWarehouseHelper()->getCatalogInventoryHelper();
    }
    /**
     * Retrieve stock identifier
     *
     * @return mixed
     */
    public function getStockId()
    {
        return $this->getData('stock_id');
    }
    /**
     * Get warehouse
     * 
     * @return Innoexts_Warehouse_Model_Warehouse
     */
    public function getWarehouse()
    {
        $helper = $this->getWarehouseHelper();
        return $helper->getWarehouseByStockId($this->getStockId());
    }
    /**
     * Load available item data by product
     * 
     * @param   Mage_Catalog_Model_Product $product
     * @return  Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    public function loadAvailableByProduct($product) {
        $this->_getResource()->loadAvailableByProduct($this, $product);
        $this->setOrigData();
        return $this;
    }
    /**
     * Get default data
     * 
     * @return array
     */
    protected function getDefaultData()
    {
        return array('qty' => 0, 'is_in_stock' => 0, 'stock_status' => 0, 'manage_stock' => 1, );
    }
    /**
     * Adding available stock data to product
     * 
     * @param   Mage_Catalog_Model_Product $product
     * @return  Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    public function assignAvailableProduct(Mage_Catalog_Model_Product $product)
    {
        $helper                 = $this->getWarehouseHelper();
        $productPriceHelper     = $helper->getProductPriceHelper();
        $stockStatus            = $this->getCatalogInventoryHelper()->getStockStatusSingleton();
        if (!$this->getId() || !$this->getProductId()) {
            $this->loadAvailableByProduct($product);
            if (!$this->getId()) {
                $this->addData($this->getDefaultData());
            }
        }
        $this->setProduct($product);
        $product->setStockItem($this);
        $product->setIsInStock($this->getIsInStock());
        $stockStatus->assignProduct($product, $this->getStockId(), $this->getStockStatus());
        $productPriceHelper->applyPrices($product);
        return $this;
    }
    /**
     * Adding stock data to product
     * 
     * @param   Mage_Catalog_Model_Product $product
     * @return  Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    public function assignProduct(Mage_Catalog_Model_Product $product)
    {
        $helper                 = $this->getWarehouseHelper();
        $productPriceHelper     = $helper->getProductPriceHelper();
        $stockStatus            = $this->getCatalogInventoryHelper()->getStockStatusSingleton();
        if (!$this->getId() || !$this->getProductId()) {
            if ($this->getWarehouseHelper()->getConfig()->isMultipleMode() && !$this->getStockId()) {
                $this->loadAvailableByProduct($product);
            } else {
                $this->loadByProduct($product);
            }
            if (!$this->getId()) {
                $this->addData($this->getDefaultData());
            }
        }
        $this->setProduct($product);
        $product->setStockItem($this);
        $product->setIsInStock($this->getIsInStock());
        $stockStatus->assignProduct($product, $this->getStockId(), $this->getStockStatus());
        $productPriceHelper->applyPrices($product);
        return $this;
    }
    /**
     * Get maximal stock quantity
     * 
     * @param float $origQty
     * @return float
     */
    public function getMaxStockQty($origQty)
    {
        $qty = $this->getQty();
        if ($qty > $origQty) {
            $qty = $origQty;
        }
        if (!$this->getManageStock()) {
            return $qty;
        }
        if (!$this->getIsInStock()) {
            return false;
        }
        if (!is_numeric($qty)) {
            $qty = Mage::app()->getLocale()->getNumber($qty);
        }
        if (!$this->getIsQtyDecimal()) {
            $qty = (int) $qty;
        }
        $qtyIncrements = $this->getQtyIncrements();
        if (!$qtyIncrements){
            $qtyIncrements = $this->getDefaultQtyIncrements();
        }
        if ($qtyIncrements && ($qty % $qtyIncrements != 0)) {
            $qty = floor($qty / $qtyIncrements) * $qtyIncrements;
        }
        if ($this->getMinSaleQty() && ($qty < $this->getMinSaleQty())) {
            return false;
        }
        if ($this->getMaxSaleQty() && ($qty > $this->getMaxSaleQty())) {
            $qty = $this->getMaxSaleQty();
        }
        if (!$qty) {
            return false;
        }
        return $qty;
    }
    /**
     * Returns product instance
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return $this->_productInstance ? $this->_productInstance : $this->_getData('product');
    }
}