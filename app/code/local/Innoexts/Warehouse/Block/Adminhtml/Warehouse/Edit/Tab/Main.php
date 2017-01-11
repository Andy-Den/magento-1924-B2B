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
 * Warehouse edit main tab
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Main 
    extends Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Abstract 
{
    /**
     * Form field set identifier
     * 
     * @var string
     */
    protected $_formFieldsetId = 'main_fieldset';
    /**
     * Form field set legend
     * 
     * @var string
     */
    protected $_formFieldsetLegend = 'General';
    /**
     * Tab title
     * 
     * @var string
     */
    protected $_title = 'General';
    /**
     * Prepare form before rendering HTML
     *
     * @return Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $helper = $this->getWarehouseHelper();
        $model = $this->getModel();
        $isElementDisabled = ($this->isSaveAllowed()) ? false : true;
        $fieldset = $this->getFieldset();
        if ($model->getId()) {
            $fieldset->addField('warehouse_id', 'hidden', array(
                'name' => 'warehouse_id', 
                'value' => $model->getId()
            ));
        }
        $fieldset->addField('code', 'text', array(
            'name'      => 'code', 
            'label'     => $helper->__('Code'), 
            'title'     => $helper->__('Code'), 
            'required'  => true, 
            'disabled'  => $isElementDisabled, 
            'value'     => $model->getCode(), 
        ));
        $fieldset->addField('title', 'text', array(
            'name'      => 'title', 
            'label'     => $helper->__('Title'), 
            'title'     => $helper->__('Title'), 
            'required'  => true, 
            'disabled'  => $isElementDisabled, 
            'value'     => $model->getTitle(), 
        ));
        $fieldset->addField('description', 'textarea', array(
            'name'      => 'description', 
            'label'     => $helper->__('Description'), 
            'title'     => $helper->__('Description'), 
            'required'  => false, 
            'disabled'  => $isElementDisabled, 
            'value'     => $model->getDescription(), 
        ));
        $config = $helper->getConfig();
        if ($config->isPriorityEnabled()) {
            $fieldset->addField('priority', 'text', array(
                'name'      => 'priority', 
                'label'     => $helper->__('Priority'), 
                'title'     => $helper->__('Priority'), 
                'required'  => false, 
                'disabled'  => $isElementDisabled, 
                'value'     => $model->getPriority(), 
            ));
        }
        $this->dispatchPrepareFormEvent();
        return $this;
    }
}