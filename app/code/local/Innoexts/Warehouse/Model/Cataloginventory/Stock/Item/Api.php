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
 * Stock item api
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Cataloginventory_Stock_Item_Api 
    extends Mage_CatalogInventory_Model_Stock_Item_Api 
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
     * Get stock items
     * 
     * @param array $productIds
     * @param int $stockId
     * 
     * @return array
     */
    protected function _items($productIds, $stockId)
    {
        $helper                 = $this->getWarehouseHelper();
        if (!is_array($productIds)) {
            $productIds             = array($productIds);
        }
        $product                = Mage::getModel('catalog/product');
        foreach ($productIds as &$productId) {
            if ($newId = $product->getIdBySku($productId)) {
                $productId              = $newId;
            }
        }
        $collection             = Mage::getModel('catalog/product')
            ->getCollection()
            ->setFlag('ignore_stock_items', true)
            ->addFieldToFilter('entity_id', array('in' => $productIds))
            ->load();
        $helper->getCatalogInventoryHelper()
            ->getStock($stockId)
            ->addItemsToProducts($collection);
        $items                  = array();
        foreach ($collection as $product) {
            $stockItem              = $product->getStockItem();
            $item                   = array(
                'product_id'            => $product->getId(), 
                'sku'                   => $product->getSku(), 
            );
            if ($stockItem) {
                $item['qty']            = $stockItem->getQty();
                $item['is_in_stock']    = $stockItem->getIsInStock();
                $item['stock_id']       = $stockItem->getStockId();
            } else {
                $item['qty']            = 0;
                $item['is_in_stock']    = 0;
                $item['stock_id']       = $stockId;
            }
            $items[]                = $item;
        }
        return $items;
    }
    /**
     * Get stock items
     * 
     * @param array $productIds
     * 
     * @return array
     */
    public function items($productIds)
    {
        return $this->_items($productIds, $this->getWarehouseHelper()->getDefaultStockId());
    }
    /**
     * Get stock items by stock identifier
     * 
     * @param array $productIds
     * @param int $stockId
     * 
     * @return array
     */
    public function itemsByStock($productIds, $stockId)
    {
        return $this->_items($productIds, $stockId);
    }
    /**
     * Update stock
     * 
     * @param int $productId
     * @param int $stockId
     * @param mixed $data
     * 
     * @return bool
     */
    protected function _update($productId, $data, $stockId)
    {
        $helper                 = $this->getWarehouseHelper();
        $product                = Mage::getModel('catalog/product');
        $newId                  = $product->getIdBySku($productId);
        if ($newId) {
            $productId              = $newId;
        }
        $storeId                = $this->_getStoreId();
        $product->setStoreId($storeId)
            ->load($productId);
        if (!$product->getId()) {
            $this->_fault('not_exists');
        }
        $stockItems             = $product->getStockItems();
        $stockItem              = (isset($stockItems[$stockId])) ? $stockItems[$stockId] : null;
        if (!$stockItem) {
            $stockItem              = array();
        }
        $stockItem['stock_id']  = $stockId;
        if (isset($data['qty'])) {
            $stockItem['qty']       = $data['qty'];
        }
        if (isset($data['is_in_stock'])) {
            $stockItem['is_in_stock'] = $data['is_in_stock'];
        }
        if (isset($data['manage_stock'])) {
            $stockItem['manage_stock'] = $data['manage_stock'];
        }
        if (isset($data['use_config_manage_stock'])) {
            $stockItem['use_config_manage_stock'] = $data['use_config_manage_stock'];
        }
        if ($helper->getVersionHelper()->isGe1700()) {
            if (isset($data['use_config_backorders'])) {
                $stockItem['use_config_backorders'] = $data['use_config_backorders'];
            }
            if (isset($data['backorders'])) {
                $stockItem['backorders'] = $data['backorders'];
            }
        }
        $stocksData             = array();
        $stocksData[$stockId]   = $stockItem;
        $product->setStocksData($stocksData);
        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }
        return true;
    }
    /**
     * Update stock
     * 
     * @param int $productId
     * @param int $stockId
     * @param mixed $data
     * 
     * @return bool
     */
    public function update($productId, $data)
    {
        return $this->_update($productId, $data, $this->getWarehouseHelper()->getDefaultStockId());
    }
    /**
     * Update stock item by stock
     * 
     * @param int $productId
     * @param int $stockId
     * @param mixed $data
     * 
     * @return bool
     */
    public function updateByStock($productId, $data, $stockId)
    {
        return $this->_update($productId, $data, $stockId);
    }
}