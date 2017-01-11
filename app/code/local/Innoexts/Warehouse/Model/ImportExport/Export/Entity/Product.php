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
 * Export product
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_ImportExport_Export_Entity_Product 
    extends Mage_ImportExport_Model_Export_Entity_Product 
{
    /**
     * Get warehouse helper
     * 
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Prepare catalog inventory
     *
     * @param  array $productIds
     * 
     * @return array
     */
    protected function _prepareCatalogInventory(array $productIds)
    {
        $select = $this->_connection->select()
            ->from(Mage::getResourceModel('cataloginventory/stock_item')->getMainTable())
            ->where('product_id IN (?)', $productIds);
        $select->where('stock_id='.$this->getWarehouseHelper()->getDefaultStockId());
        $stmt = $this->_connection->query($select);
        $stockItemRows = array();
        while ($stockItemRow = $stmt->fetch()) {
            $productId = $stockItemRow['product_id'];
            unset(
                $stockItemRow['item_id'], $stockItemRow['product_id'], $stockItemRow['low_stock_date'],
                $stockItemRow['stock_id'], $stockItemRow['stock_status_changed_automatically']
            );
            $stockItemRows[$productId] = $stockItemRow;
        }
        return $stockItemRows;
    }
}