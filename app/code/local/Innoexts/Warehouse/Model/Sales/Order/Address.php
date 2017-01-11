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
 * Order address
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Sales_Order_Address 
    extends Mage_Sales_Model_Order_Address 
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
     * Get warehouse
     * 
     * @return Innoexts_Warehouse_Model_Warehouse
     */
    public function getWarehouse()
    {
        $warehouse = null;
        if ($stockId = $this->getStockId()) {
            $warehouse = $this->getWarehouseHelper()->getWarehouseByStockId($stockId);
        }
        return $warehouse;
    }
    /**
     * Get warehouse title
     * 
     * @return string
     */
    public function getWarehouseTitle()
    {
        $warehouse = $this->getWarehouse();
        return ($warehouse) ? $warehouse->getTitle() : null;
    }
}