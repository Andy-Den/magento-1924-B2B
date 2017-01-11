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
 * Warehouse area
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Area 
    extends Innoexts_Core_Model_Area_Abstract 
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('warehouse/warehouse_area');
    }
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
     * Get filters
     * 
     * @return array
     */
    protected function getFilters()
    {
        $filters = array(
            'country_id'     => $this->getCountryFilter(), 
            'region_id'      => $this->getRegionFilter('country_id'), 
            'is_zip_range'   => $this->getTextFilter(), 
            'zip'            => $this->getZipFilter(), 
            'from_zip'       => $this->getTextFilter(), 
            'to_zip'         => $this->getTextFilter(), 
        );
        return $filters;
    }
    /**
     * Get validators
     * 
     * @return array
     */
    protected function getValidators()
    {
        $helper = $this->getWarehouseHelper();
        $validators = array(
            'country_id'     => $this->getTextValidator(false, 0, 4), 
            'region_id'      => $this->getIntegerValidator(false, 0), 
            'is_zip_range'   => $this->getIntegerValidator(false, 0), 
        );
        $isZipRange = $this->getIsZipRange();
        if ($isZipRange) {
            $maxZipValue = 9999999999;
            $fromZip = (int) $this->getFromZip();
            $validators['from_zip'] = $this->getIntegerValidator(true, 1, $maxZipValue);
            $validators['to_zip'] = $this->getIntegerValidator(true, $fromZip, $maxZipValue);
        } else {
            $validators['zip'] = $this->getTextValidator(false, 0, 10);
        }
        return $validators;
    }
    /**
     * Validate catalog inventory stock
     *
     * @throws Mage_Core_Exception
     * 
     * @return bool
     */
    public function validate()
    {
        $helper = $this->getWarehouseHelper();
        if (parent::validate()) {
            $isZipRange = $this->getIsZipRange();
            if ($isZipRange) {
                $this->setZip($this->getFromZip().'-'.$this->getToZip());
            } else {
                $this->setFromZip(null);
                $this->setToZip(null);
            }
            $errorMessages = array();
            $warehouseArea = Mage::getModel('warehouse/warehouse_area')->loadByRequest($this);
            if ($warehouseArea->getId()) {
                array_push($errorMessages, $helper->__('Duplicate area.'));
            }
            if (count($errorMessages)) {
                Mage::throwException(join("\n", $errorMessages));
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * Processing object before save data
     *
     * @return Innoexts_Warehouse_Model_Warehouse_Area
     */
    protected function _beforeSave()
    {
        $this->filter();
        $this->validate();
        return parent::_beforeSave();
    }
    /**
     * Load warehouse area by request
     * 
     * @param Varien_Object $request
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Area
     */
    public function loadByRequest(Varien_Object $request)
    {
        $this->_getResource()->loadByRequest($this, $request);
        $this->setOrigData();
        return $this;
    }
}