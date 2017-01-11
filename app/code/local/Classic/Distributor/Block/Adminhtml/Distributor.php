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
 * Distributor admin block
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Block_Adminhtml_Distributor extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_controller         = 'adminhtml_distributor';
        $this->_blockGroup         = 'classic_distributor';
        parent::__construct();
        $this->_headerText         = Mage::helper('classic_distributor')->__('Distributor');
        $this->_updateButton('add', 'label', Mage::helper('classic_distributor')->__('Add Distributor'));

    }
}
