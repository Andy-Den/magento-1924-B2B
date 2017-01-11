<?php

class MageFM_MoIP_Model_Source_Cctype
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'VI', 'label' => 'Visa'),
            array('value' => 'MC', 'label' => 'MasterCard'),
            array('value' => 'AE', 'label' => 'American Express'),
            array('value' => 'DN', 'label' => 'Diners'),
            array('value' => 'HC', 'label' => 'Hipercard'),
        );
    }

}
