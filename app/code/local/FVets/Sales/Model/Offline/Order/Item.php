<?php

class FVets_Sales_Model_Offline_Order_Item extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('fvets_sales/offline_order_item');
	}

	public function getProduct()
	{
		$product = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToFilter('id_erp', $this->getItemIdErp())
			->getFirstItem();

		return Mage::getModel('catalog/product')->load($product->getId());
	}
}
