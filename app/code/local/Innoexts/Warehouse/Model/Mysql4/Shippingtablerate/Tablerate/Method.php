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
 * Shipping table rate method resource
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Shippingtablerate_Tablerate_Method 
    extends Mage_Core_Model_Mysql4_Abstract 
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
     * Constructor
     */
    protected function _construct() {
        $this->_init('shippingtablerate/tablerate_method', 'method_id');
    }
    /**
     * Load method by code
     * 
     * @param Innoexts_Warehouse_Model_Shippingtablerate_Tablerate_Method $tablerateMethod
     * @param string $code
     * @param int $exclude
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Shippingtablerate_Tablerate_Method
     */
    public function loadByCode($tablerateMethod, $code, $exclude = null)
    {
        $adapter    = $this->_getReadAdapter();
        $select     = $adapter->select()->from($this->getMainTable());
        $select->where('code = ?', $code);
        if ($exclude) {
            $select->where('method_id <> ?', $exclude);
        }
        $row        = $adapter->fetchRow($select);
        if ($row && !empty($row)) {
            $tablerateMethod->setData($row);
        }
        $this->_afterLoad($tablerateMethod);
        return $this;
    }
    /**
     * Load method by name
     * 
     * @param Innoexts_Warehouse_Model_Shippingtablerate_Tablerate_Method $tablerateMethod
     * @param string $name
     * @param int $exclude
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Shippingtablerate_Tablerate_Method
     */
    public function loadByName($tablerateMethod, $name, $exclude = null)
    {
        $adapter    = $this->_getReadAdapter();
        $select     = $adapter->select()->from($this->getMainTable());
        $select->where('name = ?', $name);
        if ($exclude) {
            $select->where('method_id <> ?', $exclude);
        }
        $row = $adapter->fetchRow($select);
        if ($row && !empty($row)) {
            $tablerateMethod->setData($row);
        }
        $this->_afterLoad($tablerateMethod);
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
}