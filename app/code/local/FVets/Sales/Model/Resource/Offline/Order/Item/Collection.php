<?php

class FVets_Sales_Model_Resource_Offline_Order_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('fvets_sales/offline_order_item');
	}

}