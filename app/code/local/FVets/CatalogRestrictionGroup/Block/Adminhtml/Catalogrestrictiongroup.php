<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group admin block
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_controller         = 'adminhtml_catalogrestrictiongroup';
        $this->_blockGroup         = 'fvets_catalogrestrictiongroup';
        parent::__construct();
        $this->_headerText         = Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Group');
        $this->_updateButton('add', 'label', Mage::helper('fvets_catalogrestrictiongroup')->__('Add Restriction Group'));

    }
}
