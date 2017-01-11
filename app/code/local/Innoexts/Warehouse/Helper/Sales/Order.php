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
 * Order helper
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Helper_Sales_Order 
    extends Mage_Core_Helper_Abstract 
{
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    public function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get pending payment state
     * 
     * @return string
     */
    public function getPendingPaymentState()
    {
        return 'pending_payment';
    }
    /**
     * Check if order has pending payment state
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return boolean
     */
    public function isPendingPayment($order)
    {
        return ($order->getState() == $this->getPendingPaymentState()) ? true : false;
    }
    /**
     * Get order make payment URL
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return string
     */
    public function getMakePaymentUrl($order)
    {
        return Mage::getModel('core/url')->getUrl('warehouse/sales_order/makepayment', array('order_id' => $order->getId()));
    }
}