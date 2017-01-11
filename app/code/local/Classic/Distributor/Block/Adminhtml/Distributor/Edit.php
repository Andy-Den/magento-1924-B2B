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
 * Distributor admin edit form
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Block_Adminhtml_Distributor_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'classic_distributor';
        $this->_controller = 'adminhtml_distributor';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('classic_distributor')->__('Save Distributor')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('classic_distributor')->__('Delete Distributor')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('classic_distributor')->__('Save And Continue Edit'),
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
     * @author Douglas Borella Ianitsky
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_distributor') && Mage::registry('current_distributor')->getId()) {
            return Mage::helper('classic_distributor')->__(
                "Edit Distributor '%s'",
                $this->escapeHtml(Mage::registry('current_distributor')->getName())
            );
        } else {
            return Mage::helper('classic_distributor')->__('Add Distributor');
        }
    }
}
