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
 * Order resource
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Sales_Order 
    extends Mage_Sales_Model_Mysql4_Order 
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
     * Update records in grid table
     *
     * @param array|int $ids
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Sales_Order
     */
    public function updateGridRecords($ids)
    {
        parent::updateGridRecords($ids);
        if ($this->_grid) {
            if (!is_array($ids)) {
                $ids = array($ids);
            }
            if ($this->_eventPrefix && $this->_eventObject) {
                $proxy = new Varien_Object();
                $proxy->setIds($ids)->setData($this->_eventObject, $this);
                Mage::dispatchEvent($this->_eventPrefix . '_update_grid_records', array('proxy' => $proxy));
                $ids = $proxy->getIds();
            }
            if (empty($ids)) { return $this; }
            $orderItemTable = $this->getTable('sales/order_item');
            $orderWarehouseTable = $this->getTable('warehouse/order_grid_warehouse');
            $write = $this->_getWriteAdapter();
            $write->delete($orderWarehouseTable, 'entity_id IN '.$write->quoteInto('(?)', $ids));
            $select = $write->select()
                ->from(array('order_item_table' => $orderItemTable), array('entity_id' => 'order_id', 'stock_id' => 'stock_id'))
                ->where('order_item_table.order_id IN(?)', $ids)
                ->distinct(true);
            $write->query($select->insertFromSelect($orderWarehouseTable, array('entity_id', 'stock_id', ), false));
        }
        return $this;
    }
}