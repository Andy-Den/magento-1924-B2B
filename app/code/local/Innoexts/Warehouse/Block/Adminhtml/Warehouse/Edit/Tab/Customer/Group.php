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
 * Warehouse customer group tab
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Customer_Group 
    extends Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Abstract 
{
    /**
     * Form field set identifier
     * 
     * @var string
     */
    protected $_formFieldsetId = 'customer_group_fieldset';
    /**
     * Form field set legend
     * 
     * @var string
     */
    protected $_formFieldsetLegend = 'Customer Groups';
    /**
     * Tab title
     * 
     * @var string
     */
    protected $_title = 'Customer Groups';
    /**
     * Prepare form before rendering HTML
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Customer_Group
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $helper             = $this->getWarehouseHelper();
        $model              = $this->getModel();
        $isElementDisabled  = ($this->isSaveAllowed()) ? false : true;
        $fieldset           = $this->getFieldset();
        $customerGroupIdsElement = $fieldset->addField('customer_group_ids', 'text', array(
            'name'      => 'customer_group_ids', 
            'label'     => $helper->__('Customer Groups'), 
            'title'     => $helper->__('Customer Groups'), 
            'required'  => false, 
            'value'     => $model->getCustomerGroupIds(), 
        ));
        $customerGroupIdsElement->setRenderer(
            $this->getLayout()->createBlock('warehouse/adminhtml_warehouse_edit_tab_customer_group_renderer')
        );
        $this->dispatchPrepareFormEvent();
        return $this;
    }
}