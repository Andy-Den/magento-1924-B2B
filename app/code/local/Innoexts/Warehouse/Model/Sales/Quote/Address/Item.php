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
 * Quote address item
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Sales_Quote_Address_Item 
    extends Mage_Sales_Model_Quote_Address_Item 
{
    /**
     * Stock item model
     *
     * @var Mage_CatalogInventory_Model_Stock_Item
     */
    protected $_stockItem;
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
     * Get default stock identifier
     */
    public function getDefaultStockId()
    {
        return $this->getWarehouseHelper()->getDefaultStockId();
    }
    /**
     * Retrieve stock identifier
     * 
     * @return int
     */
    public function getStockId()
    {
        $quote = $this->getQuote();
        if ($quote) {
            $address = $quote->getAddress();
            if ($address && $address->getStockId()) {
                return $address->getStockId();
            } else {
                return $this->getDefaultStockId();
            }
        } else {
            return $this->getDefaultStockId();
        }
    }
    /**
     * Get stock item
     * 
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function getStockItem()
    {
        if (!$this->_stockItem) {
            $this->_stockItem = $this->getWarehouseHelper()->getCatalogInventoryHelper()->getStockItem($this->getStockId());
            $this->_stockItem->assignProduct($this->getProduct());
        }
        return $this->_stockItem;
    }
}