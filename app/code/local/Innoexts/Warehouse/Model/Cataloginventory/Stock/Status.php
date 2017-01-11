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
 * Stock status
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Cataloginventory_Stock_Status 
    extends Mage_CatalogInventory_Model_Stock_Status 
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
     * Get catalog inventory helper
     * 
     * @return Innoexts_Warehouse_Helper_Cataloginventory
     */
    protected function getCatalogInventoryHelper()
    {
        return $this->getWarehouseHelper()->getCatalogInventoryHelper();
    }
    /**
     * Get stock item model
     *
     * @return Innoexts_Warehouse_Model_Cataloginventory_Stock_Status
     */
    public function getStockItemModel()
    {
        return $this->getCatalogInventoryHelper()->getStockItem($this->getStockId());
    }
    /**
     * Rebuild stock status for all products
     *
     * @param int $websiteId
     * 
     * @return Innoexts_Warehouse_Model_Cataloginventory_Stock_Status
     */
    public function rebuild($websiteId = null)
    {
        $lastProductId = 0;
        $stocksIds = $this->getCatalogInventoryHelper()->getStockIds();
        while (true) {
            $productCollection = $this->getResource()->getProductCollection($lastProductId);
            if (!$productCollection) break;
            foreach ($productCollection as $productId => $productType) {
                $lastProductId = $productId;
                foreach ($stocksIds as $stockId) {
                    $this->setStockId($stockId);
                    $this->updateStatus($productId, $productType, $websiteId);
                }
            }
        }
        return $this;
    }
    /**
     * Add stock status to prepare index select
     *
     * @param Varien_Db_Select $select
     * @param Mage_Core_Model_Website $website
     * 
     * @return Innoexts_Warehouse_Model_Cataloginventory_Stock_Status
     */
    public function addStockStatusToSelect(Varien_Db_Select $select, Mage_Core_Model_Website $website)
    {
        $this->_getResource()->addStockStatusToSelect_($select, $website, $this->getStockId());
        return $this;
    }
    /**
     * Add only is in stock products filter to product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * 
     * @return Innoexts_Warehouse_Model_Cataloginventory_Stock_Status
     */
    public function addIsInStockFilterToCollection($collection)
    {
        $this->_getResource()->addIsInStockFilterToCollection_($collection, $this->getStockId());
        return $this;
    }
    /**
     * Add stock status limitation to catalog product price index select object
     *
     * @param Varien_Db_Select $select
     * @param string|Zend_Db_Expr $entityField
     * @param string|Zend_Db_Expr $websiteField
     * @param string|Zend_Db_Expr $stockField
     * 
     * @return Innoexts_Warehouse_Model_Cataloginventory_Stock_Status
     */
    public function prepareCatalogProductIndexSelect2(Varien_Db_Select $select, $entityField, $websiteField, $stockField)
    {
        if (Mage::helper('cataloginventory')->isShowOutOfStock()) { 
            return $this; 
        }
        $this->_getResource()->prepareCatalogProductIndexSelect2($select, $entityField, $websiteField, $stockField);
        return $this;
    }
    /**
     * Add information about stock status to product collection
     * 
     * @param   Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
     * @param   int|null $websiteId
     * @param   int|null $stockId
     * 
     * @return  Mage_CatalogInventory_Model_Stock_Status
     */
    public function addStockStatusToProducts($productCollection, $websiteId = null, $stockId = null)
    {
        if ($websiteId === null) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
            if ((int)$websiteId == 0 && $productCollection->getStoreId()) {
                $websiteId = Mage::app()->getStore($productCollection->getStoreId())->getWebsiteId();
            }
        }
        $productIds = array();
        foreach ($productCollection as $product) {
            $productIds[] = $product->getId();
        }
        if (!empty($productIds)) {
            $stockStatuses = $this->_getResource()->getProductStatus($productIds, $websiteId, $stockId);
            foreach ($stockStatuses as $productId => $status) {
                if ($product = $productCollection->getItemById($productId)) {
                    $product->setIsSalable($status);
                }
            }
        }
        foreach ($productCollection as $product) {
            $object = new Varien_Object(array(
                'is_in_stock'   => $product->getData('is_salable'), 
                'stock_id'      => $stockId, 
            ));
            $product->setStockItem($object);
        }
        return $this;
    }
}