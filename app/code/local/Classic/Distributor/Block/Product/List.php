<?php

class Classic_Distributor_Block_Product_List extends Mage_Page_Block_Html
{

	private $_product = null;

	public function getDistributors()
	{
		$brand = Mage::getResourceModel('catalog/product')->getAttributeRawValue($this->getProduct()->getId(), 'brand', Mage::app()->getStore()->getId());

		$collection =  Mage::getModel('classic_distributor/distributor')->getCollection()
			->addBrandFilter($brand);

		return $collection;
	}

	protected function getProduct()
	{
		if ($this->_product === null)
		{
			$this->_product = Mage::registry('product');
		}

		return $this->_product;
	}
}

?>