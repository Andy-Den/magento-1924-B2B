<?php

class FVets_CheckoutSplit_Block_Sales_Order_Totals extends Mage_Sales_Block_Order_Totals
{

	/**
	 * Get order object
	 *
	 * @return Mage_Sales_Model_Order
	 */
	public function getOrder()
	{
		if ($this->hasData('order')) {
			$this->_order = $this->_getData('order');
		} elseif (Mage::registry('current_order')) {
			$this->_order = Mage::registry('current_order');
		} elseif ($this->getParentBlock()->getOrder()) {
			$this->_order = $this->getParentBlock()->getOrder();
		}

		return $this->_order;
	}

}
