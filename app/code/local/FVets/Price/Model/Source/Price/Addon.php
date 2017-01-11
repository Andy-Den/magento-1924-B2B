<?php
class FVets_Price_Model_Source_Price_Addon
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'cart', 'label' => Mage::helper('fvets_payment')->__('Cart')),
			//array('value' => 'catalog', 'label' => Mage::helper('fvets_payment')->__('Catalog')),
		);
	}
}