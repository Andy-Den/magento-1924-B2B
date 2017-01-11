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
 * Condition admin edit form
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Block_Adminhtml_Condition_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'fvets_payment';
        $this->_controller = 'adminhtml_condition';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('fvets_payment')->__('Save Condition')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('fvets_payment')->__('Delete Condition')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('fvets_payment')->__('Save And Continue Edit'),
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
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_condition') && Mage::registry('current_condition')->getId()) {
            return Mage::helper('fvets_payment')->__(
                "Edit Condition '%s'",
                $this->escapeHtml(Mage::registry('current_condition')->getName())
            );
        } else {
            return Mage::helper('fvets_payment')->__('Add Condition');
        }
    }
}
