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
 * Table rates grid
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Grid 
    extends Innoexts_ShippingTablerate_Block_Adminhtml_Tablerate_Grid 
{
    /**
     * Retrieve warehouse helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get warehouses options
     * 
     * @return array
     */
    protected function getWarehousesOptions()
    {
        $helper             = $this->getWarehouseHelper();
        $options            = array();
        $warehouses         = $helper->getWarehousesOptions(false, '*', '0');
        foreach ($warehouses as $warehouse) {
            $options[$warehouse['value']] = $warehouse['label'];
        }
        return $options;
    }
    /**
     * Get methods options
     * 
     * @return array
     */
    protected function getMethodsOptions()
    {
        $helper             = $this->getWarehouseHelper();
        $options            = array();
        $tablerateMethods   = $helper->getShippingTablerateMethodsOptions(true);
        foreach ($tablerateMethods as $tablerateMethod) {
            $options[$tablerateMethod['value']] = $tablerateMethod['label'];
        }
        return $options;
    }
    /**
     * Prepare columns
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Grid
     */
    protected function _prepareColumns()
    {
        $helper             = $this->getWarehouseHelper();
        $this->addColumn('warehouse_id', array(
            'header'    => $helper->__('Warehouse'), 
            'align'     => 'left', 
            'index'     => 'warehouse_id', 
            'type'      => 'options', 
            'options'   => $this->getWarehousesOptions(), 
        ));
        parent::_prepareColumns();
        $this->addColumnAfter('method_id', array(
            'header'    => $helper->__('Method'), 
            'align'     => 'left', 
            'index'     => 'method_id', 
            'type'      => 'options', 
            'options'   => $this->getMethodsOptions(), 
        ), 'condition_value');
        $this->sortColumnsByOrder();
        return $this;
    }
}