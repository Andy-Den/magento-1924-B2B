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
 * Cart shipping block
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Checkout_Cart_Shipping 
    extends Mage_Checkout_Block_Cart_Shipping 
{
    /**
     * Shipping rates
     * 
     * @var array
     */
    protected $_rates2;
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
     * Get addresses
     * 
     * @return array of Innoexts_Warehouse_Model_Sales_Quote_Address
     */
    public function getAddresses()
    {
        return $this->getQuote()->getAllShippingAddresses();
    }
    /**
     * Get shipping rates
     * 
     * @param int $stockId
     * 
     * @return array
     */
    public function getShippingRates2($stockId)
    {
        if (is_null($this->_rates2)) {
            $this->getAddress()->collectShippingRates()->save();
            $rates = array();
            foreach ($this->getAddresses() as $address) {
                $stockId = (int) $address->getStockId();
                if ($stockId) {
                    $rates[$stockId] = $address->getGroupedAllShippingRates();
                }
            }
            $this->_rates2 = $rates;
        }
        if (isset($this->_rates2[$stockId])) {
            return $this->_rates2[$stockId];
        } else {
            return array();
        }
    }
    /**
     * Check if no shipping rates
     * 
     * @return bool
     */
    public function isShippingRatesEmpty()
    {
        $isEmpty = true;
        $addresses = $this->getAddresses();
        if (count($addresses)) {
            $isEmpty = false;
            foreach ($addresses as $address) {
                $stockId    = (int) $address->getStockId();
                $rates      = ($stockId) ? $this->getShippingRates2($stockId) : array();
                if (!count($rates)) {
                    $isEmpty = true;
                    break;
                }
            }
        }
        return $isEmpty;
    }
    /**
     * Get address shipping method
     * 
     * @param int $stockId
     * 
     * @return string
     */
    public function getAddressShippingMethod2($stockId)
    {
        $shippingAddress = $this->getQuote()->getShippingAddress2($stockId);
        if ($shippingAddress) {
            return $shippingAddress->getShippingMethod();
        } else {
            return null;
        }
    }
    /**
     * Get shipping price
     * 
     * @param float $price
     * @param bool $flag
     * 
     * @return float
     */
    public function getShippingPrice2($stockId, $price, $flag)
    {
        $quote = $this->getQuote();
        $shippingAddress = $quote->getShippingAddress2($stockId);
        if ($shippingAddress) {
            $taxHelper = $this->getWarehouseHelper()->getTaxHelper();
            return $quote->getStore()->convertPrice($taxHelper->getShippingPrice($price, $flag, $shippingAddress), true);
        } else {
            return null;
        }
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