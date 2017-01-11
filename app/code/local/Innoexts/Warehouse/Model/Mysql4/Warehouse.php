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
 * Warehouse resource
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Warehouse 
    extends Mage_Core_Model_Mysql4_Abstract 
{
    /**
     * Constructor
     */
    protected function _construct() {
        $this->_init('warehouse/warehouse', 'warehouse_id');
    }
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
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object) {
        parent::_beforeSave($object);
        if (!$object->getId() || !$object->getCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        return $this;
    }
    /**
     * Load warehouse by code
     * 
     * @param Innoexts_Warehouse_Model_Warehouse $warehouse
     * @param string $code
     * @param int $exclude
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Warehouse
     */
    public function loadByCode(Innoexts_Warehouse_Model_Warehouse $warehouse, $code, $exclude = null)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getMainTable());
        $select->where('code = ?', $code);
        if ($exclude) {
            $select->where('warehouse_id <> ?', $exclude);
        }
        $row = $adapter->fetchRow($select);
        if ($row && !empty($row)) $warehouse->setData($row);
        $this->_afterLoad($warehouse);
        return $this;
    }
    /**
     * Load warehouse by title
     * 
     * @param Innoexts_Warehouse_Model_Warehouse $warehouse
     * @param string $title
     * @param int $exclude
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Warehouse
     */
    public function loadByTitle(Innoexts_Warehouse_Model_Warehouse $warehouse, $title, $exclude = null)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getMainTable());
        $select->where('title = ?', $title);
        if ($exclude) {
            $select->where('warehouse_id <> ?', $exclude);
        }
        $row = $adapter->fetchRow($select);
        if ($row && !empty($row)) $warehouse->setData($row);
        $this->_afterLoad($warehouse);
        return $this;
    }
    /**
     * Get write connection
     * 
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    public function getWriteConnection()
    {
        return $this->_getWriteAdapter();
    }
    /**
     * Get stores
     * 
     * @param Innoexts_Warehouse_Model_Warehouse $warehouse
     * 
     * @return array
     */
    public function getStores($warehouse)
    {
        $stores = array();
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('warehouse/warehouse_store'))
            ->where('warehouse_id = ?', $warehouse->getId());
        $query = $adapter->query($select);
        while ($row = $query->fetch()) {
            array_push($stores, $row['store_id']);
        }
        return $stores;
    }
    /**
     * Get shipping carriers
     * 
     * @param Innoexts_Warehouse_Model_Warehouse $warehouse
     * 
     * @return array
     */
    public function getShippingCarriers($warehouse)
    {
        $shippingCarriers = array();
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('warehouse/warehouse_shipping_carrier'))
            ->where('warehouse_id = ?', $warehouse->getId());
        $query = $adapter->query($select);
        while ($row = $query->fetch()) {
            array_push($shippingCarriers, $row['shipping_carrier']);
        }
        return $shippingCarriers;
    }
}