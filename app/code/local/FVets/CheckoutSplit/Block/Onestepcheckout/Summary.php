<?php

	class FVets_CheckoutSplit_Block_Onestepcheckout_Summary extends Idev_OneStepCheckout_Block_Summary
	{
		public function _beforeToHtml()
		{

			//Mage::helper('fvets_checkoutsplit')->collectTotals($this->getParentBlock()->getSalesrepToFinalTotals());
			//$this->getQuote()->setTotalsCollectedFlag(false)->collectTotals();

			return parent::_beforeToHtml();
		}
	}

?>