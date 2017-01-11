<?php
class FVets_Customer_Model_Eav_Entity_Attribute_Source_HiveActivity extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('magefm_customer')->__('Pet Shop')),
            array('value' => 2, 'label' => Mage::helper('magefm_customer')->__('Clínica Veterinária')),
            array('value' => 3, 'label' => Mage::helper('magefm_customer')->__('Médico Veterinário')),
            array('value' => 4, 'label' => Mage::helper('magefm_customer')->__('Agro')),
            array('value' => 5, 'label' => Mage::helper('magefm_customer')->__('Outros')),
        );
    }

    public function getKeyValueOptions()
    {
        $options = array();

        foreach ($this->getAllOptions() as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }
}