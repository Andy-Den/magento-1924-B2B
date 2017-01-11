<?php
class FVets_Customer_Model_Eav_Entity_Attribute_Source_Areainterest extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        return array(
            array('value' => 'Pequenos Animais', 'label' => Mage::helper('magefm_customer')->__('Pequenos Animais')),
            array('value' => 'Equinos', 'label' => Mage::helper('magefm_customer')->__('Equinos')),
            array('value' => 'Aves e/ou Suínos', 'label' => Mage::helper('magefm_customer')->__('Aves e/ou Suínos')),
            array('value' => 'Métodos Diagnósticos', 'label' => Mage::helper('magefm_customer')->__('Métodos Diagnósticos')),
            array('value' => 'Bovinos', 'label' => Mage::helper('magefm_customer')->__('Bovinos')),
        );
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option["value"]] = $option["label"];
        }
        return $_options;
    }
    
}