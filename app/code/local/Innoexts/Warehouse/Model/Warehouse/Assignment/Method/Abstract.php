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
 * Abstact warehouse assignment method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Abstract 
    extends Varien_Object 
{
    /**
     * Quote
     * 
     * @var Innoexts_Warehouse_Model_Sales_Quote
     */
    protected $_quote;
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
     * Get quote
     * 
     * @return Innoexts_Warehouse_Model_Sales_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }
    /**
     * Set quote
     * 
     * @param Innoexts_Warehouse_Model_Sales_Quote $quote
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract
     */
    public function setQuote($quote)
    {
        $this->_quote = $quote;
        return $this;
    }
    /**
     * Check if assignment method is active
     * 
     * @return bool
     */
    public function isActive()
    {
        $flag = $this->getData('active');
        if (!empty($flag) && ($flag !== 'false')) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Check if assignment method is based on shipping
     * 
     * @return bool
     */
    public function isBasedOnShipping()
    {
        $flag = $this->getData('based_on_shipping');
        if (!empty($flag) && ($flag !== 'false')) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->getWarehouseHelper()->__($this->getData('title'));
    }
    /**
     * Get description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->getWarehouseHelper()->__($this->getData('description'));
    }
    /**
     * Get store
     * 
     * @return Mage_Core_Model_Store
     */
    protected function getStore()
    {
        $store = null;
        $quote = $this->getQuote();
        if ($quote) {
            $store = $quote->getStore();
        }
        if (!$store) {
            $store = Mage::app()->getStore();
        }
        return $store;
    }
    /**
     * Get store identifier
     * 
     * @return int
     */
    protected function getStoreId()
    {
        return $this->getStore()->getId();
    }
    /**
     * Get customer group id
     * 
     * @return int
     */
    protected function getCustomerGroupId()
    {
        $customerGroupId = null;
        $quote = $this->getQuote();
        if ($quote) {
            $customerGroupId = $quote->getCustomerGroupId();
        }
        if (!$customerGroupId) {
            $customerGroupId = $this->getWarehouseHelper()
                ->getCustomerHelper()
                ->getCustomerGroupId();
        }
        return $customerGroupId;
    }
    /**
     * Get currency code
     * 
     * @return string
     */
    protected function getCurrencyCode()
    {
        $currencyCode = null;
        $quote = $this->getQuote();
        if ($quote) {
            $currencyCode = $quote->getQuoteCurrencyCode();
        }
        if (!$currencyCode) {
            $currencyCode = $this->getWarehouseHelper()
                ->getCurrencyHelper()
                ->getCurrentCode();
        }
        return $currencyCode;
    }
    /**
     * Get customer address
     * 
     * @return Varien_Object
     */
    protected function getCustomerAddress()
    {
        $helper         = $this->getWarehouseHelper();
        $address        = null;
        $addressHelper  = $helper->getAddressHelper();
        $quote          = $this->getQuote();
        if ($quote) {
            $shippingAddress = $quote->getShippingAddress();
            if ($shippingAddress && !$addressHelper->isEmpty($shippingAddress)) {
                $address = $addressHelper->cast($shippingAddress);
            }
        }
        if (!$address || $addressHelper->isEmpty($address)) {
            $customerLocatorHelper = $helper->getCustomerLocatorHelper();
            $customerAddress = $customerLocatorHelper->getCustomerAddress();
            $address = $addressHelper->cast($customerAddress);
        }
        return $address;
    }
    /**
     * Apply quote stock items
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Abstract
     */
    public function applyQuoteStockItems()
    {
        return $this;
    }
    /**
     * Get stock identifier
     * 
     * @return int|null
     */
    public function getStockId()
    {
        return null;
    }
    /**
     * Get product stock identifier
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return int|null
     */
    public function getProductStockId($product)
    {
        return null;
    }
}