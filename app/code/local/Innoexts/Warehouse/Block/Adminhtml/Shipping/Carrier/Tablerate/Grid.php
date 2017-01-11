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
 * Shipping carrier table rate grid
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Shipping_Carrier_Tablerate_Grid 
    extends Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid 
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
     * Prepare page
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Shipping_Carrier_Tablerate_Grid
     */
    protected function _preparePage()
    {
        $this->getCollection()->getSelect()->order(array(
            'warehouse_id', 
            'dest_country_id', 
            'dest_region_id', 
            'dest_zip', 
            'condition_value', 
            'method_id', 
            'price', 
        ));
        parent::_preparePage();
        return $this;
    }
    /**
     * Prepare table columns
     *
     * @return Innoexts_Warehouse_Block_Adminhtml_Shipping_Carrier_Tablerate_Grid
     */
    protected function _prepareColumns()
    {
        $helper = $this->getWarehouseHelper();
        $this->addColumn('warehouse_id', array(
            'header'        => $this->getWarehouseHelper()->__('Warehouse'), 
            'index'         => 'warehouse_id', 
            'default'       => '*', 
        ));
        parent::_prepareColumns();
        $this->addColumnAfter('method_id', array(
            'header'        => $helper->__('Method'), 
            'align'         => 'left', 
            'index'         => 'method_id', 
        ), 'condition_value');
        $this->sortColumnsByOrder();
        return $this;
    }
}