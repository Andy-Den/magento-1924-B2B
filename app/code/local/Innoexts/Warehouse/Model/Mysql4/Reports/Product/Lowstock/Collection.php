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
 * Product low stock report collection
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Reports_Product_Lowstock_Collection 
    extends Mage_Reports_Model_Mysql4_Product_Lowstock_Collection 
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
     * Join catalog inventory stock item table for further stock_item values filters
     *
     * @return Mage_Reports_Model_Mysql4_Product_Collection
     */
    public function joinInventoryItem($fields = array())
    {
        return $this;
    }
    /**
     * Add Use Manage Stock Condition to collection
     *
     * @param int|null $storeId
     * @return Mage_Reports_Model_Mysql4_Product_Collection
     */
    public function useManageStockFilter($storeId = null)
    {
        return $this;
    }
    /**
     * Add Notify Stock Qty Condition to collection
     *
     * @param int $storeId
     * @return Mage_Reports_Model_Mysql4_Product_Collection
     */
    public function useNotifyStockQtyFilter($storeId = null)
    {
        return $this;
    }
}