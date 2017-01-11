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
 * Warehouse collection
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Warehouse_Collection 
    extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    /**
     * Constructor
     */
    protected function _construct() {
        $this->_init('warehouse/warehouse');
    }
    /**
     * Retrieve options array
     * 
     * @param string $valueField
     * 
     * @return array
     */
    public function toOptionArray($valueField = 'warehouse_id')
    {
        return $this->_toOptionArray($valueField, 'title');
    }
    /**
     * Retrieve options hash array
     * 
     * @param string $valueField
     * 
     * @return array
     */
    public function toOptionHash($valueField = 'warehouse_id')
    {
        return $this->_toOptionHash($valueField, 'title');
    }
    /**
     * Before load
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _beforeLoad()
    {
        Mage::dispatchEvent('warehouse_collection_load_before', array('collection' => $this));
        return parent::_beforeLoad();
    }
    /**
     * After load
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _afterLoad()
    {
        if (count($this) > 0) {
            Mage::dispatchEvent('warehouse_collection_load_after', array('collection' => $this));
        }
        return $this;
    }
}