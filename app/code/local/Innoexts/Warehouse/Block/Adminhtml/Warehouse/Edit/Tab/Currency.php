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
 * Warehouse currency tab
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Currency 
    extends Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Abstract 
{
    /**
     * Form field set identifier
     * 
     * @var string
     */
    protected $_formFieldsetId = 'currency_fieldset';
    /**
     * Form field set legend
     * 
     * @var string
     */
    protected $_formFieldsetLegend = 'Currencies';
    /**
     * Tab title
     * 
     * @var string
     */
    protected $_title = 'Currencies';
    /**
     * Prepare form before rendering HTML
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Currency
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $helper             = $this->getWarehouseHelper();
        $model              = $this->getModel();
        $isElementDisabled  = ($this->isSaveAllowed()) ? false : true;
        $fieldset           = $this->getFieldset();
        $currenciesElement = $fieldset->addField('currencies', 'text', array(
            'name'      => 'currencies', 
            'label'     => $helper->__('Currencies'), 
            'title'     => $helper->__('Currencies'), 
            'required'  => false, 
            'value'     => $model->getCurrencies(), 
        ));
        $currenciesElement->setRenderer(
            $this->getLayout()->createBlock('warehouse/adminhtml_warehouse_edit_tab_currency_renderer')
        );
        $this->dispatchPrepareFormEvent();
        return $this;
    }
}