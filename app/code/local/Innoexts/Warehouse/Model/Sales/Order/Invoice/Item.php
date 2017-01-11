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
 * Invoice item
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Sales_Order_Invoice_Item 
    extends Mage_Sales_Model_Order_Invoice_Item 
{
    /**
     * Warehouse
     *
     * @var Innoexts_Warehouse_Model_Warehouse
     */
    protected $_warehouse;
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
     * Retrieve warehouse
     *
     * @return Innoexts_Warehouse_Model_Warehouse
     */
    public function getWarehouse()
    {
        if (is_null($this->_warehouse)) {
            if ($this->getStockId()) {
                $this->_warehouse = $this->getWarehouseHelper()->getWarehouseByStockId($this->getStockId());
            }
        }
        return $this->_warehouse;
    }
    /**
     * Get warehouse title
     * 
     * @return string
     */
    public function getWarehouseTitle()
    {
        $warehouse = $this->getWarehouse();
        if ($warehouse) {
            return $warehouse->getTitle();
        } else {
            return null;
        }
    }
    /**
     * Clear invoice object data
     *
     * @param string $key data key
     * 
     * @return Innoexts_Warehouse_Model_Sales_Order_Invoice_Item
     */
    public function unsetData($key = null)
    {
        parent::unsetData($key);
        if (is_null($key)) {
            $this->_warehouse = null;
        }
        return $this;
    }
}