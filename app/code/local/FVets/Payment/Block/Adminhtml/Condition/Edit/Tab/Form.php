<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Condition edit form tab
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Block_Adminhtml_Condition_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Condition_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('condition_');
        $form->setFieldNameSuffix('condition');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'condition_form',
            array('legend' => Mage::helper('fvets_payment')->__('Condition'))
        );

        $fieldset->addField(
            'id_erp',
            'text',
            array(
                'label' => Mage::helper('fvets_payment')->__('ID ERP'),
                'name'  => 'id_erp',
            'required'  => false,

           )
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('fvets_payment')->__('Name'),
                'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'start_days',
            'text',
            array(
                'label' => Mage::helper('fvets_payment')->__('Days to start'),
                'name'  => 'start_days',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'split',
            'text',
            array(
                'label' => Mage::helper('fvets_payment')->__('Split'),
                'name'  => 'split',
            'note'	=> $this->__('Always >= 1'),
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'split_range',
            'text',
            array(
                'label' => Mage::helper('fvets_payment')->__('Split Range'),
                'name'  => 'split_range',
            'note'	=> $this->__('De quanto em quanto tempo há uma nova parcela (Para 1 unica parcela, deixe 0'),
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'price_range_begin',
            'text',
            array(
                'label' => Mage::helper('fvets_payment')->__('Price Range Begin'),
                'name'  => 'price_range_begin',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'price_range_end',
            'text',
            array(
                'label' => Mage::helper('fvets_payment')->__('Price Range End'),
                'name'  => 'price_range_end',
            'note'	=> $this->__('Use 0 (Zero) for infinit value'),
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'discount',
            'text',
            array(
                'label' => Mage::helper('fvets_payment')->__('Discount'),
                'name'  => 'discount',
                'note'	=> $this->__('Desconto em %'),
                'required'  => true,
                'class' => 'required-entry',

            )
        );

        $fieldset->addField(
            'increase',
            'text',
            array(
                'label' => Mage::helper('fvets_payment')->__('Increase'),
                'name'  => 'increase',
                'note'	=> $this->__('Acréscimo em %'),
                'required'  => true,
                'class' => 'required-entry',

            )
        );

        $fieldset->addField(
            'payment_methods',
            'multiselect',
            array(
                'label' => Mage::helper('fvets_payment')->__('Payment Methods'),
                'name'  => 'payment_methods',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> Mage::getModel('fvets_payment/condition_attribute_source_paymentmethods')->getAllOptions(false),
           )
        );

        $fieldset->addField(
            'apply_to_all',
            'select',
            array(
                'label' => Mage::helper('fvets_payment')->__('Apply to all costumers'),
                'name'  => 'apply_to_all',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('fvets_payment')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('fvets_payment')->__('No'),
                ),
            ),
           )
        );

        $fieldset->addField(
            'apply_to_groups',
            'multiselect',
            array(
                'label' => Mage::helper('fvets_payment')->__('Apply to Groups'),
                'name'  => 'apply_to_groups',
            'required'  => false,
            'class' => '',

            'values'=> Mage::getModel('fvets_payment/condition_attribute_source_applytogroups')->getAllOptions(false),
           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('fvets_payment')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('fvets_payment')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('fvets_payment')->__('Disabled'),
                    ),
                ),
            )
        );
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_condition')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_condition')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getConditionData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getConditionData());
            Mage::getSingleton('adminhtml/session')->setConditionData(null);
        } elseif (Mage::registry('current_condition')) {
            $formValues = array_merge($formValues, Mage::registry('current_condition')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
