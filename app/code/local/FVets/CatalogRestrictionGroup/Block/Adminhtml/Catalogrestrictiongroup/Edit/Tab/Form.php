<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group edit form tab
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Form
     * @author Douglas Ianitsky
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('catalogrestrictiongroup_');
        $form->setFieldNameSuffix('catalogrestrictiongroup');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'catalogrestrictiongroup_form',
            array('legend' => Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Group'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('fvets_catalogrestrictiongroup')->__('Name'),
                'name'  => 'name',
            'note'	=> $this->__('Mantenha essa nomenclatura organizada'),
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'website_id',
            'select',
            array(
                'label' => Mage::helper('fvets_catalogrestrictiongroup')->__('Website'),
                'name'  => 'website_id',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> Mage::getResourceModel('core/website_collection')->toOptionArray(),
           )
        );

        $fieldset->addField(
            'because',
            'textarea',
            array(
                'label' => Mage::helper('fvets_catalogrestrictiongroup')->__('Because'),
                'name'  => 'because',
            'note'	=> $this->__('Por que esse grupo existe? '),
            'required'  => true,
            'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('fvets_catalogrestrictiongroup')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('fvets_catalogrestrictiongroup')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('fvets_catalogrestrictiongroup')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_catalogrestrictiongroup')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getCatalogrestrictiongroupData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getCatalogrestrictiongroupData());
            Mage::getSingleton('adminhtml/session')->setCatalogrestrictiongroupData(null);
        } elseif (Mage::registry('current_catalogrestrictiongroup')) {
            $formValues = array_merge($formValues, Mage::registry('current_catalogrestrictiongroup')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
