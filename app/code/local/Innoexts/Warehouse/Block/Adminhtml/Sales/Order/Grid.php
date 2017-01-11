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
 * Orders grid
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */

if(Mage::helper('core')->isModuleEnabled('FVets_Sales')){
	class Innoexts_Warehouse_Block_Adminhtml_Sales_Order_Grid_Tmp extends FVets_Sales_Block_Adminhtml_Order_Grid {}
} else {
	class Innoexts_Warehouse_Block_Adminhtml_Sales_Order_Grid_Tmp extends Mage_Adminhtml_Block_Sales_Order_Grid {}
}

class Innoexts_Warehouse_Block_Adminhtml_Sales_Order_Grid 
    extends Innoexts_Warehouse_Block_Adminhtml_Sales_Order_Grid_Tmp
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
     * Prepare grid collection object
     *
     * @return self
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getWarehouseHelper()
            ->getAdminhtmlHelper()
            ->prepareOrderGridCollection($this);
        return $this;
    }
    /**
     * Prepare columns
     * 
     * @return self
     */
    protected function _prepareColumns()
    {
        $this->getWarehouseHelper()
            ->getAdminhtmlHelper()
            ->addStockOrderGridColumn($this);
        parent::_prepareColumns();
        return $this;
    }
}