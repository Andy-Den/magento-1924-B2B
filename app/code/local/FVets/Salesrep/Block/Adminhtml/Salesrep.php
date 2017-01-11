<?php
/**
 * FVets_Salesrep extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Sales Rep admin block
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Block_Adminhtml_Salesrep extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_controller         = 'adminhtml_salesrep';
        $this->_blockGroup         = 'fvets_salesrep';
        parent::__construct();
        $this->_headerText         = Mage::helper('fvets_salesrep')->__('Sales Rep');
        $this->_updateButton('add', 'label', Mage::helper('fvets_salesrep')->__('Add Sales Rep'));

    }
}
