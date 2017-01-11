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
 * Catalog search fulltext resource
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Catalogsearch_Fulltext 
    extends Mage_CatalogSearch_Model_Mysql4_Fulltext 
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
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    protected function getVersionHelper()
    {
        return $this->getWarehouseHelper()->getVersionHelper();
    }
    /**
     * Retrieve searchable products per store
     *
     * @param int $storeId
     * @param array $staticFields
     * @param array|int $productIds
     * @param int $lastProductId
     * @param int $limit
     * 
     * @return array
     */
    protected function _getSearchableProducts($storeId, array $staticFields, $productIds = null, $lastProductId = 0, $limit = 100)
    {
        $store  = Mage::app()->getStore($storeId);
        $adapter = $this->_getWriteAdapter();
        $conditionSelect = $adapter->select();
        $conditionSelect->from(
            array('stock_status_2' => $this->getTable('cataloginventory/stock_status'), ), 
            array('stock_id', )
        );
        $conditionSelect->where(
            '(stock_status_2.product_id = stock_status.product_id) AND '.
            '(stock_status_2.website_id = stock_status.website_id)'
        );
        $conditionSelect->order('stock_status_2.stock_status DESC');
        $conditionSelect->limit(1);
        $select = $adapter->select()
            ->useStraightJoin(true)
            ->from(
                array('e' => $this->getTable('catalog/product')),
                array_merge(array('entity_id', 'type_id'), $staticFields)
            )->join(
                array('website' => $this->getTable('catalog/product_website')),
                'website.product_id=e.entity_id AND website.website_id = '.$adapter->quote($store->getWebsiteId()), 
                array()
            )->join(
                array('stock_status' => $this->getTable('cataloginventory/stock_status')), 
                '(stock_status.product_id=e.entity_id) AND '.
                '(stock_status.website_id = '.$adapter->quote($store->getWebsiteId()).') AND '.
                '(stock_status.stock_id = ('.$conditionSelect->assemble().'))', 
                array('in_stock' => 'stock_status')
            );
        if (!is_null($productIds)) $select->where('e.entity_id IN(?)', $productIds);
        $select->where('e.entity_id > ?', $lastProductId)->limit($limit)->order('e.entity_id');
        
        if ($this->getVersionHelper()->isGe1900()) {
            Mage::dispatchEvent('prepare_catalog_product_index_select', array(
                'select'        => $select, 
                'entity_field'  => new Zend_Db_Expr('e.entity_id'), 
                'website_field' => new Zend_Db_Expr('website.website_id'), 
                'store_field'   => $storeId, 
                'stock_field'   => new Zend_Db_Expr('stock_status.stock_id'), 
            ));
        }
        
        $result = $adapter->fetchAll($select);
        if ($this->getVersionHelper()->isGe1700()) {
            return $result;
        } else {
            if ($this->_engine && $this->_engine->allowAdvancedIndex() && count($result) > 0) {
                return $this->_engine->addAdvancedIndex($result, $storeId, $productIds);
            } else {
                return $result;
            }
        }
    }
}