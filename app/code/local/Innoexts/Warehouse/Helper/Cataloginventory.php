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
 * Catalog inventory helper
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Helper_Cataloginventory 
    extends Mage_Core_Helper_Abstract 
{
    /**
     * Stocks
     * 
     * @var array of Mage_Cataloginventory_Model_Stock
     */
    protected $_stocks;
    /**
     * Stock item cache
     * 
     * @var array of array of Mage_Cataloginventory_Model_Stock_Item
     */
    protected $_stockItemCache;
    /**
     * Stock items cache
     * 
     * @var array of array of Mage_Cataloginventory_Model_Stock_Item
     */
    protected $_stockItemsCache;
    /**
     * Get default stock identifier
     * 
     * @return int
     */
    public function getDefaultStockId()
    {
        return Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
    }
    /**
     * Get stock
     *
     * @param int $stockId
     * 
     * @return Mage_Cataloginventory_Model_Stock
     */
    public function getStock($stockId = null)
    {
        $stock = Mage::getModel('cataloginventory/stock');
        if ($stockId) {
            $stock->setStockId($stockId);
        }
        return $stock;
    }
    /**
     * Get stock singleton
     *
     * @param int $stockId
     * 
     * @return Mage_Cataloginventory_Model_Stock
     */
    public function getStockSingleton($stockId = null)
    {
        $stock = Mage::getSingleton('cataloginventory/stock');
        if ($stockId) {
            $stock->setStockId($stockId);
        }
        return $stock;
    }
    /**
     * Get stock resource
     * 
     * @param int $stockId
     * 
     * @return Mage_Cataloginventory_Model_Mysql4_Stock
     */
    public function getStockResource($stockId = null)
    {
        $stock = Mage::getResourceModel('cataloginventory/stock');
        if ($stockId) {
            $stock->setStockId($stockId);
        }
        return $stock;
    }
    /**
     * Get stock resource singleton
     * 
     * @param   int $stockId
     * 
     * @return  Mage_Cataloginventory_Model_Mysql4_Stock
     */
    public function getStockResourceSingleton($stockId = null)
    {
        $stock = Mage::getResourceSingleton('cataloginventory/stock');
        if ($stockId) {
            $stock->setStockId($stockId);
        }
        return $stock;
    }
    /**
     * Get stock item
     *
     * @param   int $stockId
     * 
     * @return  Mage_Cataloginventory_Model_Stock_Item
     */
    public function getStockItem($stockId = null)
    {
        $stockItem = Mage::getModel('cataloginventory/stock_item');
        if ($stockId) {
            $stockItem->setStockId($stockId);
        }
        return $stockItem;
    }
    /**
     * Get stock item singleton
     *
     * @param   int $stockId
     * 
     * @return  Mage_Cataloginventory_Model_Stock_Item
     */
    public function getStockItemSingleton($stockId = null)
    {
        $stockItem = Mage::getSingleton('cataloginventory/stock_item');
        if ($stockId) {
            $stockItem->setStockId($stockId);
        }
        return $stockItem;
    }
    /**
     * Get stock item cached
     * 
     * @param int $productId
     * @param int $stockId
     * 
     * @return Mage_Cataloginventory_Model_Stock_Item
     */
    public function getStockItemCached($productId, $stockId = null)
    {
        $stockId = ($stockId) ? $stockId : 0;
        if (!isset($this->_stockItemCache[$productId]) || !isset($this->_stockItemCache[$productId][$stockId])) {
            $this->_stockItemCache[$productId][$stockId] = $this->getStockItem($stockId);
        }
        return $this->_stockItemCache[$productId][$stockId];
    }
    /**
     * Unset stock item cached 
     * 
     * @param int $productId
     * 
     * @return Innoexts_Warehouse_Helper_Cataloginventory
     */
    public function unsetStockItemCached($productId)
    {
        if (isset($this->_stockItemCache[$productId])) {
            unset($this->_stockItemCache[$productId]);
        }
        return $this;
    }
    /**
     * Get stock items cached
     * 
     * @param int $productId 
     * 
     * @return array of Mage_Cataloginventory_Model_Stock_Item
     */
    public function getStockItemsCached($productId)
    {
        if (!isset($this->_stockItemsCache[$productId])) {
            $this->_stockItemsCache[$productId] = array();
            foreach ($this->getStockItemCollection($productId, true) as $stockItem) {
                $stockId = (int) $stockItem->getStockId();
                $this->_stockItemsCache[$productId][$stockId] = $stockItem;
            }
        }
        return $this->_stockItemsCache[$productId];
    }
    /**
     * Unset stock items cached 
     * 
     * @param int $productId
     * 
     * @return Innoexts_Warehouse_Helper_Cataloginventory
     */
    public function unsetStockItemsCached($productId)
    {
        if (isset($this->_stockItemsCache[$productId])) {
            unset($this->_stockItemsCache[$productId]);
        }
        return $this;
    }
    /**
     * Get stock status
     *
     * @param int $stockId
     * 
     * @return Mage_Cataloginventory_Model_Stock_Status
     */
    public function getStockStatus($stockId = null)
    {
        $stockStatus = Mage::getModel('cataloginventory/stock_status');
        if ($stockId) {
            $stockStatus->setStockId($stockId);
        }
        return $stockStatus;
    }
    /**
     * Get stock status singleton
     *
     * @param int $stockId
     * 
     * @return Mage_Cataloginventory_Model_Stock_Status
     */
    public function getStockStatusSingleton($stockId = null)
    {
        $stockStatus = Mage::getSingleton('cataloginventory/stock_status');
        if ($stockId) {
            $stockStatus->setStockId($stockId);
        }
        return $stockStatus;
    }
    /**
     * Get stock collection
     * 
     * @return Mage_CatalogInventory_Model_Mysql4_Stock_Collection
     */
    public function getStockCollection()
    {
        return $this->getStockSingleton()->getCollection();
    }
    /**
     * Get stock item collection
     * 
     * @param int|null $productId
     * @param bool $inStockOnly
     * 
     * @return Mage_CatalogInventory_Model_Mysql4_Stock_Item_Collection
     */
    public function getStockItemCollection($productId = null, $inStockOnly = false)
    {
        $collection = $this->getStockItemSingleton()->getCollection();
        if (!is_null($productId)) {
            $collection->addProductsFilter(array($productId));
        }
        if ($inStockOnly) {
            $collection->addInStockFilter($this->getManageStock());
        }
        return $collection;
    }
    /**
     * Get stocks
     * 
     * @return array of Mage_Cataloginventory_Model_Stock
     */
    public function getStocks()
    {
        if (is_null($this->_stocks)) {
            $stocks = array();
            foreach ($this->getStockCollection() as $stock) {
                $stocks[$stock->getId()] = $stock;
            }
            $this->_stocks = $stocks;
        }
        return $this->_stocks;
    }
    /**
     * Get stock ids
     * 
     * @return array
     */
    public function getStockIds()
    {
        return array_keys($this->getStocks());
    }
    /**
     * Check if stock id exists
     * 
     * @param int $stockId
     * 
     * @return bool
     */
    public function isStockIdExists($stockId)
    {
        $stockIds = $this->getStockIds();
        return in_array($stockId, $stockIds);
    }
    /**
     * Get manage stock config option value
     * 
     * @return int
     */
    public function getManageStock()
    {
        return (int) Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
    }
    /**
     * Get notify stock qty config option value
     * 
     * @return int
     */
    public function getNotifyStockQty()
    {
        return (int) Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY);
    }
    /**
     * Get stock item options (used in config)
     *
     * @return array
     */
    public function getConfigItemOptions()
    {
        return Mage::helper('cataloginventory')->getConfigItemOptions();
    }
}