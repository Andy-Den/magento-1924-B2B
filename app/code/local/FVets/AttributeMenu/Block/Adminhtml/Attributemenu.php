<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * AttributeMenu admin block
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Block_Adminhtml_Attributemenu extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_controller         = 'adminhtml_attributemenu';
        $this->_blockGroup         = 'fvets_attributemenu';
        parent::__construct();
        $this->_headerText         = Mage::helper('fvets_attributemenu')->__('AttributeMenu');
        $this->_updateButton('add', 'label', Mage::helper('fvets_attributemenu')->__('Add AttributeMenu'));

    }
}
