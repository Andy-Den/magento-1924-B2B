<?php
/**
 * FVets_TablePrice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Table Price admin edit form
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Block_Adminhtml_Tableprice_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_blockGroup = 'fvets_tableprice';
        $this->_controller = 'adminhtml_tableprice';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('fvets_tableprice')->__('Save Table Price')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('fvets_tableprice')->__('Delete Table Price')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('fvets_tableprice')->__('Save And Continue Edit'),
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
        if (Mage::registry('current_tableprice') && Mage::registry('current_tableprice')->getId()) {
            return Mage::helper('fvets_tableprice')->__(
                "Edit Table Price '%s'",
                $this->escapeHtml(Mage::registry('current_tableprice')->getName())
            );
        } else {
            return Mage::helper('fvets_tableprice')->__('Add Table Price');
        }
    }
}
