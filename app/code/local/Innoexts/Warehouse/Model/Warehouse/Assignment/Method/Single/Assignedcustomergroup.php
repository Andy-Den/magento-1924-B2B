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
 * Assigned customer group single warehouse assignment method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Single_Assignedcustomergroup 
    extends Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Single_Abstract 
{
    /**
     * Get stock id
     * 
     * @return int
     */
    protected function _getStockId()
    {
        $helper     = $this->getWarehouseHelper();
        $stockId    = $helper->getStockIdByCustomerGroupId($this->getCustomerGroupId());
        if (!$stockId) {
            $stockId = $helper->getDefaultStockId();
        }
        return $stockId;
    }
}