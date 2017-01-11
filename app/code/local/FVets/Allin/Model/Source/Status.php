<?php

class FVets_Allin_Model_Source_Status extends Mage_Eav_Model_Entity_Attribute_Source_Table
{

	public function getAllOptions()
	{
		if ($this->_options === null) {
			$this->_options = array(
				array(
					'label' => 'Inválido',
					'value' => 'I',
				),
				array(
					'label' => 'Válido',
					'value' => 'V',
				)
			);
			//array('I' => 'Inválido', 'V' => 'Válido');
		}
		return $this->_options;
	}

	public function toOptionArray()
    {
        return array(
            array(                
                'label' => 'Inválido',
                'value' => 'I',
            ),
            array(                
                'label' => 'Válido',
                'value' => 'V',
            ),
        );
    }
}
