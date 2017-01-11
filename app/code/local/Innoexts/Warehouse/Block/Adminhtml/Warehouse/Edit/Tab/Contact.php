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
 * Warehouse contact tab
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Contact 
    extends Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Abstract 
{
    /**
     * Form field set identifier
     * 
     * @var string
     */
    protected $_formFieldsetId = 'contact_fieldset';
    /**
     * Form field set legend
     * 
     * @var string
     */
    protected $_formFieldsetLegend = 'Contact';
    /**
     * Tab title
     * 
     * @var string
     */
    protected $_title = 'Contact';
    /**
     * Prepare form before rendering HTML
     *
     * @return Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Contact
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $helper             = $this->getWarehouseHelper();
        $model              = $this->getModel();
        $isElementDisabled  = ($this->isSaveAllowed()) ? false : true;
        $fieldset           = $this->getFieldset();
        $fieldset->addField('notify', 'checkbox', array(
            'name'      => 'notify', 
            'label'     => $helper->__('Notify?'), 
            'title'     => $helper->__('Notify?'), 
            'required'  => false, 
            'disabled'  => $isElementDisabled, 
            'checked'   => (($model->getNotify()) ? true : false), 
            'value'     => 1, 
        ));
        $fieldset->addField('contact_name', 'text', array(
            'name'      => 'contact_name', 
            'label'     => $helper->__('Name'), 
            'title'     => $helper->__('Name'), 
            'required'  => false, 
            'disabled'  => $isElementDisabled, 
            'value'     => $model->getContactName(), 
        ));
        $fieldset->addField('contact_email', 'text', array(
            'name'      => 'contact_email', 
            'label'     => $helper->__('Email'), 
            'title'     => $helper->__('Email'), 
            'required'  => false, 
            'disabled'  => $isElementDisabled, 
            'value'     => $model->getContactEmail(), 
        ));
        $this->dispatchPrepareFormEvent();
        return $this;
    }
}