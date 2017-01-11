<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * AttributeMenu admin edit form
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'fvets_attributemenu';
        $this->_controller = 'adminhtml_attributemenu';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('fvets_attributemenu')->__('Save AttributeMenu')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('fvets_attributemenu')->__('Delete AttributeMenu')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('fvets_attributemenu')->__('Save And Continue Edit'),
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
     * @author Ultimate Module Creator
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_attributemenu') && Mage::registry('current_attributemenu')->getId()) {
            return Mage::helper('fvets_attributemenu')->__(
                "Edit AttributeMenu '%s'",
                $this->escapeHtml(Mage::registry('current_attributemenu')->getName())
            );
        } else {
            return Mage::helper('fvets_attributemenu')->__('Add AttributeMenu');
        }
    }
}
