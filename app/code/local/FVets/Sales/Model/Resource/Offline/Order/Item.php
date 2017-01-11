<?php

class FVets_Sales_Model_Resource_Offline_Order_Item extends Mage_Core_Model_Resource_Db_Abstract
{
	/**
	 * initialize resource model
	 *
	 * @access protected
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @author Douglas Borella Ianitsky
	 */
	protected function  _construct()
	{
		$this->_init('fvets_sales/offline_order_item', 'entity_id');
	}
}