<?php

class FVets_Sales_Model_Offline_Order extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('fvets_sales/offline_order');
	}

	public function getAllItems()
	{
		$items = Mage::getModel('fvets_sales/offline_order_item')
			->getCollection()
			->addFieldToFilter('offline_order_id', $this->getId());

		return $items;
	}
}
