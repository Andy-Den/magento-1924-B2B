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
require_once 'Mage/Adminhtml/controllers/Sales/Order/CreateController.php';
/**
 * Orders creation process controller
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Adminhtml_Sales_Order_CreateController 
    extends Mage_Adminhtml_Sales_Order_CreateController 
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
     * Initialize order creation session data
     * 
     * @return self
     */
    protected function _initSession()
    {
        $helper                 = $this->getWarehouseHelper();
        parent::_initSession();
        if (!$helper->getConfig()->isMultipleMode()) {
            $stockId                = (int) $this->getRequest()
                ->getParam('stock_id');
            if ($stockId && $helper->isStockIdExists($stockId)) {
                $helper->setSessionStockId($stockId);
                $orderCreate            = $this->_getOrderCreateModel();
                $orderCreate->setStockId(null);
                $orderCreate->setRecollect(true);
            }
        }
        return $this;
    }
    /**
     * Saving quote and create order
     */
    public function saveAction()
    {
        $helper                 = $this->getWarehouseHelper();
        try {
            if ($helper->getVersionHelper()->isGe1510()) {
                $this->_processActionData('save');
            } else {
                $this->_processData('save');
            }
            $paymentData            = $this->getRequest()->getPost('payment');
            if ($paymentData) {
                if ($helper->getVersionHelper()->isGe1800()) {
                    $paymentData['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_INTERNAL | 
                    Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY | 
                    Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY | 
                    Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX | 
                    Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                }
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }
            $orderCreate            = $this->_getOrderCreateModel()
                ->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('order'));
            $orderCreate->getQuote()
                ->reapplyStocks();
            $orderCreate->getQuote()
                ->collectTotals();
            $orders                 = $orderCreate->createOrder();
            $this->_getSession()
                ->clear();
            if (count($orders) > 1) {
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess($helper->__('The orders has been created.'));
                $this->_redirect('*/sales_order');
            } else {
                $order = array_shift($orders);
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess($this->__('The order has been created.'));
                if ($helper->getVersionHelper()->isGe1800()) {
                    if (Mage::getSingleton('admin/session')
                        ->isAllowed('sales/order/actions/view')) {
                        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
                    } else {
                        $this->_redirect('*/sales_order/index');
                    }
                } else {
                    $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
                }
            }
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $this->_getOrderCreateModel()
                ->saveQuote();
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()
                    ->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e){
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()
                    ->addError($message);
            }
            $this->_redirect('*/*/');
        }
        catch (Exception $e){
            $this->_getSession()
                ->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $this->_redirect('*/*/');
        }
    }
}