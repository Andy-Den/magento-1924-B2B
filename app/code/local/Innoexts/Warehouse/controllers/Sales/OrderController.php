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
 * Order controller
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Sales_OrderController 
    extends Mage_Core_Controller_Front_Action 
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
     * Customer order pending payment
     */
    public function pendingpaymentAction()
    {
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            $this->_forward('noRoute');
            return false;
        }
        $helper = $this->getWarehouseHelper();
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()
            ->getBlock('head')
            ->setTitle($helper->__('My Pending Payments'));
        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
    /**
     * Make payment action
     */
    public function makepaymentAction()
    {
        $helper         = $this->getWarehouseHelper();
        $orderHelper    = $helper->getOrderHelper();
        $orderId        = (int) $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            $this->_forward('noRoute');
            return false;
        }
        $order          = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            $this->_forward('noRoute');
            return false;
        }
        $currentOrder   = null;
        $customerSession = Mage::getSingleton('customer/session');
        if ($customerSession->isLoggedIn()) {
            if ($order->getCustomerId() == $customerSession->getCustomerId()) {
                $currentOrder = $order;
            }
        } else {
            $orderIds = Mage::getSingleton('core/session')->getOrderIds();
            if ($orderIds && is_array($orderIds) && in_array($orderId, $orderIds)) {
                $currentOrder = $order;
            } else {
                Mage::helper('sales/guest')->loadValidOrder();
                $currentOrder = Mage::registry('current_order');
            }
        }
        if (!$currentOrder || (!$orderHelper->isPendingPayment($currentOrder))) {
            $this->_forward('noRoute');
            return false;
        }
        $paymentMethod = $currentOrder->getPayment()->getMethodInstance();
        if ($paymentMethod) {
            $redirectUrl = $paymentMethod->getOrderPlaceRedirectUrl();
            if ($redirectUrl) {
                $checkoutSession = Mage::getSingleton('checkout/session');
                $checkoutSession->setLastOrderId($order->getId())
                    ->setRedirectUrl($redirectUrl)
                    ->setLastRealOrderId($order->getIncrementId());
                $agreement = $order->getPayment()->getBillingAgreement();
                if ($agreement) {
                    $checkoutSession->setLastBillingAgreementId($agreement->getId());
                }
                $this->_redirectUrl($redirectUrl);
            } else {
                $this->_forward('noRoute');
                return false;
            }
        } else {
            $this->_forward('noRoute');
            return false;
        }
    }
}