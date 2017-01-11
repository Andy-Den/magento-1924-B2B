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
 * Table Price admin block
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Block_Adminhtml_Tableprice extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_controller         = 'adminhtml_tableprice';
        $this->_blockGroup         = 'fvets_tableprice';
        parent::__construct();
        $this->_headerText         = Mage::helper('fvets_tableprice')->__('Table Price');
        $this->_updateButton('add', 'label', Mage::helper('fvets_tableprice')->__('Add Table Price'));

    }
}
