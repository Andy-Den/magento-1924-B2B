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
 * Shipping table rate resource
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Shippingtablerate_Tablerate 
    extends Innoexts_ShippingTablerate_Model_Mysql4_Tablerate 
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
     * Load table rate by request
     * 
     * @param Innoexts_ShippingTablerate_Model_Tablerate $tablerate
     * @param Varien_Object $request
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Shippingtablerate_Tablerate
     */
    public function loadByRequest(Innoexts_ShippingTablerate_Model_Tablerate $tablerate, Varien_Object $request)
    {
        $adapter            = $this->_getReadAdapter();
        $select             = $adapter->select()->from($this->getMainTable());
        $conditions         = array();
        if ($request->getId()) {
            array_push($conditions, '(pk != '.$adapter->quote($request->getId()).')');
        }
        $websiteId          = ($request->getWebsiteId()) ? $request->getWebsiteId() : '0';
        $warehouseId        = ($request->getWarehouseId()) ? $request->getWarehouseId() : '0';
        $destCountryId      = ($request->getDestCountryId()) ? $request->getDestCountryId() : '0';
        $destRegionId       = ($request->getDestRegionId()) ? $request->getDestRegionId() : '0';
        $destZip            = ($request->getDestZip()) ? $request->getDestZip() : '';
        $conditionName      = ($request->getConditionName()) ? $request->getConditionName() : '';
        $conditionValue     = ($request->getConditionValue()) ? $request->getConditionValue() : '';
        $methodId           = ($request->getMethodId()) ?       $request->getMethodId() : '';
        array_push($conditions, '(website_id = '.$adapter->quote($websiteId).')');
        array_push($conditions, '(warehouse_id = '.$adapter->quote($warehouseId).')');
        array_push($conditions, '(dest_country_id = '.$adapter->quote($destCountryId).')');
        array_push($conditions, '(dest_region_id = '.$adapter->quote($destRegionId).')');
        array_push($conditions, '(dest_zip = '.$adapter->quote($destZip).')');
        array_push($conditions, '(condition_name = '.$adapter->quote($conditionName).')');
        array_push($conditions, '(condition_value = '.$adapter->quote($conditionValue).')');
        array_push($conditions, '(method_id = '.$adapter->quote($methodId).')');
        $select->where(implode(' AND ', $conditions));
        $select->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row && !empty($row)) $tablerate->setData($row);
        $this->_afterLoad($tablerate);
        return $this;
    }
}