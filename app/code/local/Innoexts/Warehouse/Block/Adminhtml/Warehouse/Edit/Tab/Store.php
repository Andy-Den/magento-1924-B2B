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
 * Warehouse store tab
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Store 
    extends Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Abstract 
{
    /**
     * Form field set identifier
     * 
     * @var string
     */
    protected $_formFieldsetId = 'store_fieldset';
    /**
     * Form field set legend
     * 
     * @var string
     */
    protected $_formFieldsetLegend = 'Stores';
    /**
     * Tab title
     * 
     * @var string
     */
    protected $_title = 'Stores';
    /**
     * Prepare form before rendering HTML
     *
     * @return Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Store
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $helper             = $this->getWarehouseHelper();
        $model              = $this->getModel();
        $isElementDisabled  = ($this->isSaveAllowed()) ? false : true;
        $fieldset           = $this->getFieldset();
        $storeIdsElement = $fieldset->addField('store_ids', 'text', array(
            'name'      => 'store_ids', 
            'label'     => $helper->__('Stores'), 
            'title'     => $helper->__('Stores'), 
            'required'  => false, 
            'value'     => $model->getStoreIds(), 
        ));
        $storeIdsElement->setRenderer(
            $this->getLayout()->createBlock('warehouse/adminhtml_warehouse_edit_tab_store_renderer')
        );
        $this->dispatchPrepareFormEvent();
        return $this;
    }
}