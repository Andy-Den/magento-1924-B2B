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
 * Sales order pending payment
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Sales_Order_Pendingpayment 
    extends Mage_Core_Block_Template 
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
     * Constructor
     */
    public function __construct()
    {
        $helper         = $this->getWarehouseHelper();
        $orderHelper    = $helper->getOrderHelper();
        parent::__construct();
        $this->setTemplate('warehouse/sales/order/pendingpayment.phtml');
        $customer       = Mage::getSingleton('customer/session')->getCustomer();
        $orders         = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addFieldToFilter('state', $orderHelper->getPendingPaymentState())
            ->setOrder('created_at', 'desc')
            ->setFlag('appendStockIds');
        $this->setOrders($orders);
        $layout         = Mage::app()->getFrontController()->getAction()->getLayout();
        $rootBlock      = $layout->getBlock('root');
        if ($rootBlock) {
            $rootBlock->setHeaderTitle($helper->__('My Pending Payments'));
        }
    }
    /**
     * Prepare layout
     * 
     * @return Innoexts_Warehouse_Block_Sales_Order_Pendingpayment
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()
            ->createBlock('page/html_pager', 'warehouse.sales.order.pendingpayment.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }
    /**
     * Get pager HTML
     * 
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    /**
     * Get make payment URL
     * 
     * @param Mage_Sales_Model_Order $order
     * 
     * @return string
     */
    public function getMakePaymentUrl($order)
    {
        return $this->getWarehouseHelper()->getOrderHelper()->getMakePaymentUrl($order);
    }
    /**
     * Get back URL
     * 
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}