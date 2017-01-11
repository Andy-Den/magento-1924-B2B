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
 * Sales order create shipping method form
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Sales_Order_Create_Shipping_Method_Form 
    extends Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form 
{
    /**
     * Shipping methods
     * 
     * @var array
     */
    protected $_shippingMethods;
    /**
     * Before to html
     * 
     * return Innoexts_Warehouse_Block_Adminhtml_Sales_Order_Create_Shipping_Method_Form
     */
    protected function _beforeToHtml()
    {
        $this->setTemplate('warehouse/sales/order/create/shipping/method/form.phtml');
        parent::_beforeToHtml();
        return $this;
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
     * Get warehouses
     * 
     * @return array of Innoexts_Warehouse_Model_Warehouse
     */
    public function getWarehouses()
    {
        return $this->getQuote()->getWarehouses();
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
    public function _getShippingRates($stockId)
    {
        if (is_null($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();
            $rates = array();
            foreach ($this->getAddresses() as $address) {
                if ($address->getStockId()) {
                    $rates[$address->getStockId()] = $address->getGroupedAllShippingRates();
                }
            }
            $this->_rates = $rates;
        }
        if (isset($this->_rates[$stockId])) {
            return $this->_rates[$stockId];
        } else {
            return array();
        }
    }
    /**
     * Get shipping method
     * 
     * @param int $stockId
     * 
     * @return string
     */
    public function _getShippingMethod($stockId)
    {
        if (is_null($this->_shippingMethods)) {
            $shippingMethods = array();
            foreach ($this->getAddresses() as $address) {
                if ($address->getStockId()) {
                    $shippingMethods[$address->getStockId()] = $address->getShippingMethod();
                }
            }
            $this->_shippingMethods = $shippingMethods;
        }
        if (isset($this->_shippingMethods[$stockId])) {
            return $this->_shippingMethods[$stockId];
        } else {
            return null;
        }
    }
    /**
     * Retrieve rate of active shipping method
     *
     * @return Mage_Sales_Model_Quote_Address_Rate || false
     */
    public function _getActiveMethodRate($stockId)
    {
        $rates = $this->_getShippingRates($stockId);
        if (is_array($rates)) {
            foreach ($rates as $group) {
                foreach ($group as $code => $rate) {
                    if ($rate->getCode() == $this->_getShippingMethod($stockId)) {
                        return $rate;
                    }
                }
            }
        }
        return false;
    }
    /**
     * Get shipping price
     * 
     * @param float $price
     * @param bool $flag
     * 
     * @return float
     */
    public function _getShippingPrice($stockId, $price, $flag)
    {
        $quote = $this->getQuote();
        $address = $quote->getShippingAddressByStockId($stockId);
        if ($address) {
            $store = $address->getQuote()->getStore();
            return $quote->getStore()->convertPrice(
                Mage::helper('tax')->getShippingPrice($price, $flag, $address, null, $store), 
                true
            );
        } else {
            return null;
        }
    }
}