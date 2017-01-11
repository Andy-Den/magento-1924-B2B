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
 * Shipping table rate
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Shippingtablerate_Tablerate 
    extends Innoexts_ShippingTablerate_Model_Tablerate 
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
     * Filter warehouse
     * 
     * @param mixed $value
     * 
     * @return string
     */
    public function filterWarehouse($value)
    {
        $helper = $this->getWarehouseHelper();
        if ($value && ($value != '*')) {
            $warehouses = $helper->getWarehousesHash();
            if (isset($warehouses[$value])) {
                $value = $value;
            } else if (in_array($value, $warehouses)) {
                $value = array_search($value, $warehouses);
            } else $value = '0';
        } else $value = '0';
        return $value;
    }
    /**
     * Filter method
     * 
     * @param mixed $value
     * 
     * @return string
     */
    public function filterMethod($value)
    {
        $helper             = $this->getWarehouseHelper();
        $tablerateMethods   = $helper->getShippingTablerateMethodsHash();
        if (isset($tablerateMethods[$value])) {
            $value = $value;
        } else if (in_array($value, $tablerateMethods)) {
            $value = array_search($value, $tablerateMethods);
        } else {
            $value = null;
        }
        return $value;
    }
    /**
     * Get warehouse filter
     * 
     * @return Zend_Filter
     */
    protected function getWarehouseFilter()
    {
        return $this->getTextFilter()->appendFilter(new Zend_Filter_Callback(array(
            'callback' => array($this, 'filterWarehouse'), 
        )));
    }
    /**
     * Get method filter
     * 
     * @return Zend_Filter
     */
    protected function getMethodFilter()
    {
        return $this->getTextFilter()->appendFilter(new Zend_Filter_Callback(array(
            'callback' => array($this, 'filterMethod'), 
        )));
    }
    /**
     * Get filters
     * 
     * @return array
     */
    protected function getFilters()
    {
        $filters = parent::getFilters();
        $filters['warehouse_id']        = $this->getWarehouseFilter();
        $filters['method_id']           = $this->getMethodFilter();
        return $filters;
    }
    /**
     * Get validators
     * 
     * @return array
     */
    protected function getValidators()
    {
        $validators = parent::getValidators();
        $validators['warehouse_id']     = $this->getIntegerValidator(false, 0);
        $validators['method_id']        = $this->getIntegerValidator(true, 0);
        $validators['condition_name']   = $this->getTextValidator(true, 0, 30);
        return $validators;
    }
    /**
     * Get title
     * 
     * @return string
     */
    public function getTitle()
    {
        $helper     = $this->getWarehouseHelper();
        $title      = parent::getTitle();
        $pieces     = array($title);
        if ($this->getWarehouseId()) {
            $warehouse = $helper->getWarehouse($this->getWarehouseId());
        } else {
            $warehouse = null;
        }
        array_push($pieces, ($warehouse) ? $warehouse->getTitle() : '*');
        if ($this->getMethodId()) {
            $tablerateMethod = $helper->getShippingTablerateMethod($this->getMethodId());
        } else {
            $tablerateMethod = null;
        }
        array_push($pieces, ($tablerateMethod) ? $tablerateMethod->getName() : '');
        return implode(', ', $pieces);
    }
}