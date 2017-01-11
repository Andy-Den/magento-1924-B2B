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
 * Table rate edit form
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Edit_Form 
    extends Innoexts_ShippingTablerate_Block_Adminhtml_Tablerate_Edit_Form
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
     * Get warehouse values
     * 
     * @return array
     */
    protected function getWarehouseValues()
    {
        return $this->getWarehouseHelper()
            ->getWarehousesOptions(false, '*', '0');
    }
    /**
     * Get warehouse values
     * 
     * @return array
     */
    protected function getMethodValues()
    {
        return $this->getWarehouseHelper()
            ->getShippingTablerateMethodsOptions(true);
    }
    /**
     * Prepare form before rendering HTML
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Edit_Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $helper                 = $this->getWarehouseHelper();
        $model                  = $this->getModel();
        $isElementDisabled      = ($this->isSaveAllowed()) ? false : true;
        $fieldset               = $this->getFieldset();
        $fieldset->addField('warehouse_id', 'select', array(
            'name'          => 'warehouse_id', 
            'label'         => $helper->__('Warehouse'), 
            'title'         => $helper->__('Warehouse'), 
            'required'      => false, 
            'value'         => $model->getWarehouseId(), 
            'values'        => $this->getWarehouseValues(), 
            'disabled'      => $isElementDisabled, 
        ), 'website_id');
        $fieldset->addField('method_id', 'select', array(
            'name'          => 'method_id', 
            'label'         => $helper->__('Method'), 
            'title'         => $helper->__('Method'), 
            'required'      => true, 
            'value'         => $model->getMethodId(), 
            'values'        => $this->getMethodValues(), 
            'disabled'      => $isElementDisabled, 
        ), 'condition_value');
        return $this;
    }
}