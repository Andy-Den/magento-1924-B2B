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
 * Catalog search advanced resource
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Catalogsearch_Advanced 
    extends Mage_CatalogSearch_Model_Mysql4_Advanced 
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
     * Add filter by indexable attribute
     *
     * @param Mage_CatalogSearch_Model_Resource_Advanced_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * 
     * @return bool
     */
    public function addIndexableAttributeModifiedFilter($collection, $attribute, $value)
    {
        if ($attribute->getIndexType() == 'decimal') {
            $table          = $this->getTable('catalog/product_index_eav_decimal');
        } else {
            $table          = $this->getTable('catalog/product_index_eav');
        }
        $tableAlias     = 'a_' . $attribute->getAttributeId();
        $storeId        = Mage::app()->getStore()->getId();
        $select         = $collection->getSelect();
        if (is_array($value)) {
            if (isset($value['from']) && isset($value['to'])) {
                if (empty($value['from']) && empty($value['to'])) {
                    return false;
                }
            }
        }
        $select->distinct(true);
        $conditions         = array(
            "e.entity_id={$tableAlias}.entity_id", 
            "{$tableAlias}.attribute_id={$attribute->getAttributeId()}", 
            "{$tableAlias}.store_id={$storeId}", 
            "{$tableAlias}.stock_id={$this->getWarehouseHelper()
                ->getProductHelper()
                ->getCollectionStockId($collection)}", 
        );
        $select->join(array($tableAlias => $table), implode(' AND ', $conditions), array());
        if (is_array($value) && (isset($value['from']) || isset($value['to']))) {
            if (isset($value['from']) && !empty($value['from'])) {
                $select->where("{$tableAlias}.value >= ?", $value['from']);
            }
            if (isset($value['to']) && !empty($value['to'])) {
                $select->where("{$tableAlias}.value <= ?", $value['to']);
            }
            return true;
        }
        $select->where("{$tableAlias}.value IN(?)", $value);
        return true;
    }
}