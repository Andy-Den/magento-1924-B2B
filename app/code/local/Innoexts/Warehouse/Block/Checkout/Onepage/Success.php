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
 * One page checkout success
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Checkout_Onepage_Success 
    extends Mage_Checkout_Block_Onepage_Success 
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
     * Get visible states
     * 
     * @return array
     */
    protected function getVisibleStates()
    {
        $orderConfig = Mage::getSingleton('sales/order_config');
        return array_merge(
            $orderConfig->getVisibleOnFrontStates(), 
            array($this->getWarehouseHelper()->getOrderHelper()->getPendingPaymentState())
        );
    }
    /**
     * Get order print url
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return string
     */
    public function getOrderPrintUrl($order)
    {
        return $this->getUrl('sales/order/print', array('order_id'=> $order->getId()));
    }
    /**
     * Get order view url
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return string
     */
    public function getOrderViewUrl($order)
    {
        return $this->getUrl('sales/order/view', array('order_id'=> $order->getId()));
    }
    /**
     * Get order make payment URL
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return string
     */
    public function getOrderMakePaymentUrl($order)
    {
        return $this->getWarehouseHelper()->getOrderHelper()->getMakePaymentUrl($order);
    }
    /**
     * Check if order is visible
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return bool
     */
    protected function _isOrderVisible($order)
    {
        return !in_array(
            $order->getState(), 
            Mage::getSingleton('sales/order_config')->getInvisibleOnFrontStates()
        );
    }
    /**
     * Check if order print is visible
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return bool
     */
    public function isOrderPrintVisible($order)
    {
        return $this->_isOrderVisible($order);
    }
    /**
     * Check if order view is visible
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return bool
     */
    public function isOrderViewVisible($order)
    {
        return ($this->_isOrderVisible($order) && Mage::getSingleton('customer/session')->isLoggedIn());
    }
    /**
     * Check if order make payment is visible
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return boolean
     */
    public function isOrderMakePaymentVisible($order)
    {
        return $this->getWarehouseHelper()->getOrderHelper()->isPendingPayment($order);
    }
    /**
     * Before to html
     * 
     * @return Innoexts_Warehouse_Block_Checkout_Onepage_Success
     */
    protected function _beforeToHtml()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        parent::_beforeToHtml();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled()) {
            $checkoutSession    = Mage::getSingleton('checkout/session');
            $orderIds           = $checkoutSession->getOrderIds();
            if (!$orderIds) {
                $orderIds = array();
            }
            if (count($orderIds) < 2) {
                return $this;
            }
            $collection         = Mage::getResourceModel('sales/order_collection');
            $adapter            = $collection->getConnection();
            $collection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('state', array('in' => $this->getVisibleStates()))
                ->addAttributeToSort('created_at', 'asc');
            $collection->getSelect()->where($adapter->quoteInto('entity_id IN (?)', $orderIds));
            $collection->load();
            $this->setOrders($collection);
        }
        return $this;
    }
}