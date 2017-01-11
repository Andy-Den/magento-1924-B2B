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
 * @package     Innoexts_WarehousePlus
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Product tier price backend attribute resource
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Model_Mysql4_Catalog_Product_Attribute_Backend_Tierprice 
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice 
{
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_WarehousePlus_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Load tier prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @param string $storeId
     * @param string $currency
     * @param string $stockId
     * @return array
     */
    public function loadPriceData2($productId, $websiteId = null, $storeId = null, $currency = null, $stockId = null)
    {
        $adapter = $this->_getReadAdapter();
        $columns = array(
            'price_id'      => $this->getIdFieldName(), 
            'website_id'    => 'website_id', 
            'store_id'      => 'store_id', 
            'stock_id'      => 'stock_id', 
            'all_groups'    => 'all_groups', 
            'cust_group'    => 'customer_group_id', 
            'price_qty'     => 'qty', 
            'price'         => 'value', 
            'currency'      => 'currency', 
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), $columns)
            ->where('entity_id=?', $productId)
            ->order('qty');
        if (!is_null($websiteId)) {
            if ($websiteId == '0') {
                $select->where('website_id = ?', $websiteId);
            } else {
                $select->where('website_id IN(?)', array(0, $websiteId));
            }
        }
        if (!is_null($storeId)) {
            if ($storeId == '0') {
                $select->where('store_id = ?', $storeId);
            } else {
                $select->where('store_id IN(?)', array(0, $storeId));
            }
        }
        if (!is_null($currency)) {
            if ($currency == '') {
                $select->where("(currency IS NULL) OR (currency = '')", $currency);
            } else {
                $select->where("(currency = ?) OR (currency IS NULL) OR (currency = '')", $currency);
            }
        }
        if (!is_null($stockId)) {
            if ($stockId) {
                $select->where('stock_id = ?', $stockId);
            } else {
                $select->where('(stock_id IS NULL) OR (stock_id = ?)', $stockId);
            }
        }
        return $adapter->fetchAll($select);
    }
    /**
     * Delete tier prices
     *
     * @param int $productId
     * @param int $websiteId
     * @param int $storeId
     * @param int $priceId
     * @return int number of affected rows
     */
    public function deletePriceData2($productId, $websiteId = null, $storeId = null, $priceId = null)
    {
        $adapter = $this->_getWriteAdapter();
        $conds   = array(
            $adapter->quoteInto('entity_id = ?', $productId)
        );
        if (!is_null($websiteId)) {
            $conds[] = $adapter->quoteInto('website_id = ?', $websiteId);
        }
        if (!is_null($storeId)) {
            $conds[] = $adapter->quoteInto('store_id = ?', $storeId);
        }
        if (!is_null($priceId)) {
            $conds[] = $adapter->quoteInto($this->getIdFieldName() . ' = ?', $priceId);
        }
        $where = implode(' AND ', $conds);
        return $adapter->delete($this->getMainTable(), $where);
    }
}