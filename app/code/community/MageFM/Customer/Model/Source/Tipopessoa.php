<?php

class MageFM_Customer_Model_Source_Tipopessoa extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function getAllOptions()
    {
        return array(
            array('value' => 'PF', 'label' => Mage::helper('magefm_customer')->__('Pessoa física')),
            array('value' => 'PJ', 'label' => Mage::helper('magefm_customer')->__('Pessoa jurídica')),
						array('value' => 'RC', 'label' => Mage::helper('magefm_customer')->__('Representante comercial')),
						array('value' => 'AC', 'label' => Mage::helper('magefm_customer')->__('Atendente comercial')),
						array('value' => 'AA', 'label' => Mage::helper('magefm_customer')->__('Atendente comercial - Admin'))
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