<?php
/**
 * Classic_Distributor extension
 * 
 * NOTICE OF LICENSE
 *
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Distributor edit form tab
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Block_Adminhtml_Distributor_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Classic_Distributor_Block_Adminhtml_Distributor_Edit_Tab_Form
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('distributor_');
        $form->setFieldNameSuffix('distributor');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'distributor_form',
            array('legend' => Mage::helper('classic_distributor')->__('Distributor'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('classic_distributor')->__('Name'),
                'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'telephone',
            'text',
            array(
                'label' => Mage::helper('classic_distributor')->__('Telephone'),
                'name'  => 'telephone',

           )
        );

        $fieldset->addField(
            'website',
            'select',
            array(
                'label' => Mage::helper('classic_distributor')->__('Website'),
                'name'  => 'website',
            'values'=> Mage::getModel('classic_distributor/attribute_source_website')->getAllOptions(true),
           )
        );

        $fieldset->addField(
            'brands',
            'multiselect',
            array(
                'label' => Mage::helper('classic_distributor')->__('Brands'),
                'name'  => 'brands',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> Mage::getModel('classic_distributor/attribute_source_brands')->getAllOptions(false),
           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('classic_distributor')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('classic_distributor')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('classic_distributor')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_distributor')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getDistributorData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getDistributorData());
            Mage::getSingleton('adminhtml/session')->setDistributorData(null);
        } elseif (Mage::registry('current_distributor')) {
            $formValues = array_merge($formValues, Mage::registry('current_distributor')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
