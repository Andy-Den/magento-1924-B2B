<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * AttributeMenu edit form tab
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('attributemenu_');
        $form->setFieldNameSuffix('attributemenu');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'attributemenu_form',
            array('legend' => Mage::helper('fvets_attributemenu')->__('AttributeMenu'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('fvets_attributemenu')->__('Name'),
                'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

				$onchange = "submitAndReloadAnotherArea($('attributemenu_attribute').parentNode, $('attributemenu_value').parentNode, '" . $this->getAttributeOptionsUrl() . "')";
        $fieldset->addField(
            'attribute',
            'select',
            array(
                'label' => Mage::helper('fvets_attributemenu')->__('Attribute'),
                'name'  => 'attribute',
            'required'  => true,
            'class' => 'required-entry',
						'onchange' => $onchange,
            'values'=> Mage::getModel('fvets_attributemenu/attributemenu_attribute_source_attribute')->getAllOptions(true),
           )
        );

        $fieldset->addField(
            'value',
            'multiselect',
            array(
                'label' => Mage::helper('fvets_attributemenu')->__('Value'),
                'name'  => 'value',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> Mage::getModel('fvets_attributemenu/attributemenu_attribute_source_value')->getAllOptions(true),
           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('fvets_attributemenu')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('fvets_attributemenu')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('fvets_attributemenu')->__('Disabled'),
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
            Mage::registry('current_attributemenu')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_attributemenu')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getAttributemenuData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getAttributemenuData());
            Mage::getSingleton('adminhtml/session')->setAttributemenuData(null);
        } elseif (Mage::registry('current_attributemenu')) {
            $formValues = array_merge($formValues, Mage::registry('current_attributemenu')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }

	public function getAttributeOptionsUrl()
	{
		return $this->getUrl('*/*/getAttributeOptions');
	}
}
