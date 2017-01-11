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
 * Warehouse area resource
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Warehouse_Area 
    extends Mage_Core_Model_Mysql4_Abstract 
{
    /**
     * Constructor
     */
    protected function _construct() {
        $this->_init('warehouse/warehouse_area', 'warehouse_area_id');
    }
    /**
     * Load warehouse area by request
     * 
     * @param Innoexts_Warehouse_Model_Warehouse_Area $warehouseArea
     * @param Varien_Object $request
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Warehouse_Area
     */
    public function loadByRequest(Innoexts_Warehouse_Model_Warehouse_Area $warehouseArea, Varien_Object $request)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getMainTable());
        $conditions = array();
        if ($request->getId()) {
            array_push($conditions, '(warehouse_area_id != '.$adapter->quote($request->getId()).')');
        }
        $countryId  = ($request->getCountryId()) ? $request->getCountryId() : '0';
        $regionId   = ($request->getRegionId()) ? $request->getRegionId() : '0';
        $zip        = ($request->getZip()) ? $request->getZip() : '';
        array_push($conditions, '(warehouse_id = '.$adapter->quote($request->getWarehouseId()).')');
        array_push($conditions, '(country_id = '.$adapter->quote($countryId).')');
        array_push($conditions, '(region_id = '.$adapter->quote($regionId).')');
        array_push($conditions, '(zip = '.$adapter->quote($zip).')');
        $select->where(implode(' AND ', $conditions));
        $select->limit(1);
        $row = $adapter->fetchRow($select);
        if ($row && !empty($row)) {
            $warehouseArea->setData($row);
        }
        $this->_afterLoad($warehouseArea);
        return $this;
    }
}