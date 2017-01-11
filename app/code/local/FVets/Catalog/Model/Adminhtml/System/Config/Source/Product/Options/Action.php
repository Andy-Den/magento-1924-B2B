<?php

class FVets_Catalog_Model_Adminhtml_System_Config_Source_Product_Options_Action
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'increase', 'label' => Mage::helper('adminhtml')->__('Increase')),
            array('value' => 'decrease', 'label' => Mage::helper('adminhtml')->__('Decrease'))
        );
    }
}