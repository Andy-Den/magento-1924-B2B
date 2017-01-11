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
 * Condition admin block
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Block_Adminhtml_Condition extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_condition';
        $this->_blockGroup         = 'fvets_payment';
        parent::__construct();
        $this->_headerText         = Mage::helper('fvets_payment')->__('Condition');
        $this->_updateButton('add', 'label', Mage::helper('fvets_payment')->__('Add Condition'));

    }
}
