<?php
class FVets_CheckoutSplit_Block_Cart_Split_Subtotals extends Mage_Checkout_Block_Cart_Totals
{

	public function _beforeToHtml()
	{
		$this->_totals = NULL;

		return parent::_beforeToHtml();
	}

	/*public function getSubtotal()
	{
		return $this->getQuote()->getSubtotal();
	}

	public function getDiscount()
	{
		return $this->getQuote()->getDiscountAmount();
	}

	public function getTotal()
	{
		$value = $this->getQuote()->getTotal();

		//Add minimum price error message
		$storeId = $this->getQuote()->getStoreId();
		if (Mage::getStoreConfigFlag('sales/minimum_order/active', $storeId))
		{
			if ($value < Mage::getStoreConfig('sales/minimum_order/amount', $storeId))
			{
				$this->getMessagesBlock()->addNotice(Mage::getStoreConfig('sales/minimum_order/description'));
			}
		}

		return $value;
	}*/

}