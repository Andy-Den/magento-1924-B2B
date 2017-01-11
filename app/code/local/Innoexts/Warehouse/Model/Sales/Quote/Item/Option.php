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
 * Quote item option
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Sales_Quote_Item_Option 
    extends Mage_Sales_Model_Quote_Item_Option 
{
    /**
     * Stock item model
     *
     * @var Mage_CatalogInventory_Model_Stock_Item
     */
    protected $_stockItem;
    /**
     * Stock items
     * 
     * @var array of Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    protected $_stockItems;
    /**
     * In stock stock items
     * 
     * @var array of Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    protected $_inStockStockItems;
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    public function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get default stock identifier
     */
    public function getDefaultStockId()
    {
        return $this->getWarehouseHelper()->getDefaultStockId();
    }
    /**
     * Clone quote item option
     *
     * @return Innoexts_Warehouse_Model_Sales_Quote_Item_Option
     */
    public function __clone()
    {
        parent::__clone();
        $this->_stockItem           = null;
        $this->_stockItems          = null;
        $this->_inStockStockItems   = null;
        return $this;
    }
    /**
     * Retrieve stock identifier
     * 
     * @return int
     */
    public function getStockId()
    {
        $stockId = $this->_getData('stock_id');
        if (!$stockId) {
            $item = $this->getItem();
            if ($item) {
                return $item->getStockId();
            } else {
                return $this->getDefaultStockId();
            }
        } else {
            return $stockId;
        }
    }
    /**
     * Get stock item
     *
     * @return  Mage_CatalogInventory_Model_Stock_Item
     */
    public function getStockItem()
    {
        $stockId = $this->getStockId();
        if ($stockId) {
            if ($this->_stockItem && ($this->_stockItem->getStockId() != $stockId)) {
                $this->_stockItem = null;
            }
            if (!$this->_stockItem) {
                if ($this->getProduct()) {
                    $this->_stockItem = $this->getWarehouseHelper()->getCatalogInventoryHelper()->getStockItem($stockId);
                    $this->_stockItem->assignProduct($this->getProduct());
                }
            }
        }
        return $this->_stockItem;
    }
    /**
     * Unset stock item
     * 
     * @return Innoexts_Warehouse_Model_Sales_Quote_Item_Option
     */
    public function unsetStockItem()
    {
        $this->_stockItem = null;
        return $this;
    }
    /**
     * Get stock items collection
     * 
     * @return Mage_CatalogInventory_Model_Mysql4_Stock_Item_Collection
     */
    protected function getStockItemsCollection()
    {
        return $this->getWarehouseHelper()
            ->getCatalogInventoryHelper()
            ->getStockItemCollection($this->getProductId(), true);
    }
    /**
     * Get stock items
     * 
     * @return array of Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    public function getStockItems()
    {
        if (is_null($this->_stockItems)) {
            $stockItems = array();
            foreach ($this->getStockItemsCollection() as $stockItem) {
                $stockItems[$stockItem->getStockId()] = $stockItem;
            }
            $this->_stockItems = $stockItems;
        }
        return $this->_stockItems;
    }
    /**
     * Unset stock items
     * 
     * @return Innoexts_Warehouse_Model_Sales_Quote_Item
     */
    public function unsetStockItems()
    {
        $this->_stockItems = null;
    }
    /**
     * Get stock identifiers
     */
    public function getStockIds()
    {
        $stockIds = array();
        foreach ($this->getStockItems() as $stockId => $stockItem) {
            $stockIds[$stockId] = $stockId;
        }
        return $stockIds;
    }
    /**
     * Get in stock stock items
     * 
     * @return array of Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    public function getInStockStockItems()
    {
        if ($this->getLastCheckQty() != $this->getQty()) {
            $stockItems = array();
            foreach ($this->getStockItems() as $stockItem) {
                $result = $this->checkQty($stockItem);
                if (!$result->getHasError()) {
                    $stockItem->setItemBackorders($result->getItemBackorders());
                    $stockItems[$stockItem->getStockId()] = $stockItem;
                }
            }
            $this->_inStockStockItems = $stockItems;
            $this->setLastCheckQty($this->getQty());
        }
        return $this->_inStockStockItems;
    }
    /**
     * Unset in stock stock items
     * 
     * @return Innoexts_Warehouse_Model_Sales_Quote_Item
     */
    public function unsetInStockStockItems()
    {
        $this->_inStockStockItems = null;
    }
    /**
     * Get in stock stock identifiers
     * 
     * @return array
     */
    public function getInStockStockIds()
    {
        $stockIds = array();
        foreach ($this->getInStockStockItems() as $stockItem) {
            $stockId = $stockItem->getStockId();
            $stockIds[$stockId] = $stockId;
        }
        return $stockIds;
    }
    /**
     * Clear order object data
     *
     * @param string $key data key
     * @return Innoexts_Warehouse_Model_Sales_Quote_Item_Option
     */
    public function unsetData($key=null)
    {
        parent::unsetData($key);
        if (is_null($key)) {
            $this->unsetStockItem();
            $this->unsetStockItems();
            $this->unsetInStockStockItems();
        }
        return $this;
    }
}