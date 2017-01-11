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
 * Order observer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Sales_Order_Observer 
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
     * After collection load
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_Warehouse_Model_Sales_Order_Observer
     */
    public function afterCollectionLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderCollection();
        if (!$collection || !$collection->hasFlag('appendStockIds')) {
            return $this;
        }
        $orderIds = array();
        foreach ($collection as $order) {
            array_push($orderIds, $order->getId());
        }
        if (!count($orderIds)) {
            return $this;
        }
        $orderItemTable     = $collection->getTable('sales/order_item');
        $adapter            = $collection->getConnection();
        $select             = $adapter->select()
            ->from($orderItemTable, array('order_id' => 'order_id', 'stock_id' => 'stock_id'))
            ->where($adapter->quoteInto('order_id IN (?)', $orderIds));
        $query              = $adapter->query($select);
        $stockIds           = array();
        while ($orderItem = $query->fetch()) {
            $stockId    = (isset($orderItem['stock_id'])) ? (int) $orderItem['stock_id'] : null;
            $orderId    = $orderItem['order_id'];
            if ($stockId) {
                $stockIds[$orderId][] = $stockId;
            }
        }
        foreach ($collection as $order) {
            $order->setStockIds((isset($stockIds[$order->getId()])) ? $stockIds[$order->getId()] : array());
        }
        return $this;
    }
}