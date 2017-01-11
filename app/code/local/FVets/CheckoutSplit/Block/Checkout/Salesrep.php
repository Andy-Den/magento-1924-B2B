<?php

	class FVets_CheckoutSplit_Block_Checkout_Salesrep extends FVets_Salesrep_Block_Info
	{
		function getAllVisibleSalesrep()
		{
			$return = array();

			if (is_array($this->getParentBlock()->getSalesrepToFinalTotals()))
			{
				$salesrep  = $this->getCollection();
				foreach($salesrep as $rep)
				{
					if (array_key_exists($rep->getId(), $this->getParentBlock()->getSalesrepToFinalTotals()))
					{
						$return[] = $rep;
					}
				}
			}

			return $return;
		}

		function getQuote()
		{
			return Mage::getSingleton('checkout/session')->getQuote();
		}
	}