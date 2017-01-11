<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Block_Adminhtml_Tab_Entities extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function getTabLabel()
    {
        return $this->__('Advanced: Affected Entities');
    }

    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function _beforeToHtml() {
        $this->_initForm();

        return parent::_beforeToHtml();
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();

        $model = Mage::registry('current_rule');

        if (!$model->getId())
        {
            $model
                ->setLimitOrders(true)
                ->setLimitInvoices(true)
                ->setLimitShipments(true)
                ->setLimitMemos(true)
            ;
        }

        $fieldset = $form->addFieldset('access_orders_fieldset', array('legend'=>$this->__('Affected Entities')));

        $fieldset->addField('limit_orders', 'select',
            array(
                'label' => $this->__('Limit Access To Orders'),
                'name' => 'amrolepermissions[limit_orders]',
                'values'=> array(
                    0 => 'No',
                    1 => 'Yes'
                ),
            )
        );

        $fieldset->addField('limit_invoices', 'select',
            array(
                'label' => $this->__('Limit Access To Invoices And Transactions'),
                'name' => 'amrolepermissions[limit_invoices]',
                'values'=> array(
                    0 => 'No',
                    1 => 'Yes'
                ),
            )
        );

        $fieldset->addField('limit_shipments', 'select',
            array(
                'label' => $this->__('Limit Access To Shipments'),
                'name' => 'amrolepermissions[limit_shipments]',
                'values'=> array(
                    0 => 'No',
                    1 => 'Yes'
                ),
            )
        );

        $fieldset->addField('limit_memos', 'select',
            array(
                'label' => $this->__('Limit Access To Credit Memos'),
                'name' => 'amrolepermissions[limit_memos]',
                'values'=> array(
                    0 => 'No',
                    1 => 'Yes'
                ),
            )
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
