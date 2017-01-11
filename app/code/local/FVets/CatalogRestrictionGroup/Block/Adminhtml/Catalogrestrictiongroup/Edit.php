<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group admin edit form
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'fvets_catalogrestrictiongroup';
        $this->_controller = 'adminhtml_catalogrestrictiongroup';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('fvets_catalogrestrictiongroup')->__('Save Restriction Group')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('fvets_catalogrestrictiongroup')->__('Delete Restriction Group')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('fvets_catalogrestrictiongroup')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Douglas Ianitsky
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_catalogrestrictiongroup') && Mage::registry('current_catalogrestrictiongroup')->getId()) {
            return Mage::helper('fvets_catalogrestrictiongroup')->__(
                "Edit Restriction Group '%s'",
                $this->escapeHtml(Mage::registry('current_catalogrestrictiongroup')->getName())
            );
        } else {
            return Mage::helper('fvets_catalogrestrictiongroup')->__('Add Restriction Group');
        }
    }
}
