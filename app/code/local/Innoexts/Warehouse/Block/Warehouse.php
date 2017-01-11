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
 * Warehouse
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Warehouse 
    extends Mage_Core_Block_Template 
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
     * Check if block is enabled
     * 
     * @return bool
     */
    public function isEnabled()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if (!$config->isMultipleMode() && $config->isAllowAdjustment()) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get warehouses
     * 
     * @return array of Innoexts_Warehouse_Model_Warehouse
     */
    public function getWarehouses()
    {
        return $this->getWarehouseHelper()->getWarehouses();
    }
    /**
     * Get current stock id
     * 
     * @return int
     */
    public function getCurrentStockId()
    {
        return $this->getWarehouseHelper()->getAssignmentMethodHelper()->getQuoteStockId();
    }
    /**
     * Get customer address stock distance string
     * 
     * @param int $stockId
     * 
     * @return string
     */
    public function getCustomerAddressStockDistanceString($stockId)
    {
        return $this->getWarehouseHelper()->getCustomerAddressStockDistanceString($stockId);
    }
}