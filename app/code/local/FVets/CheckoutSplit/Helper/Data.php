<?php

	class FVets_CheckoutSplit_Helper_Data extends Mage_Core_Helper_Abstract
	{
		public function collectTotals($salesrep = NULL, $quote = NULL)
		{
			if (!$quote)
			{
				$quote = $this->getQuote();
			}

			$revertSplitBySalesrep = $quote->getSplitBySalesrep();
			$revertSalesrep = $quote->getSalesrep();

			if (!is_array($salesrep))
				$salesrep = array($salesrep);

			$address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

			$objects = array(
				$quote,
				$address,
			);

			//Reseta os valores do quote e endereço para que seja calculado novamente
			foreach ($objects as $object)
			{
				//Change quote values
				$object->setSubtotal(0);
				$object->setBaseSubtotal(0);
				$object->setSubtotalWithDiscount(0);
				$object->setBaseSubtotalWithDiscount(0);
				$object->setGrandTotal(0);
				$object->setBaseGrandTotal(0);
				$object->setsubtotalInclTax(0);
				$object->setBaseSubtoalInclTax(0);
				$object->setTotalQty(0);
				$object->setItemQty(0);
				$object->setGrandTotalAmount(0);
				$object->setDiscountAmount(0);
				$object->setDiscount(0);
				$object->setIncrease(0);
				$object->setIncreaseAmount(0);

				$object->setWeight(0);
			}

			//Recalcula os valores do quote e dos endereços levando em conta os produtos de cada representante.
			foreach ($salesrep as $rep)
			{
				if ($rep)
				{
					$quote->setSplitBySalesrep(true);
					$quote->setSalesrep($rep);
				}
				else
				{
					$quote->setSplitBySalesrep(false);
					$quote->setSalesrep(NULL);
				}

				foreach ($objects as $object)
				{
					foreach ($object->getAllVisibleItems() as $item)
					{
						$object->setSubtotal($object->getSubtotal() + $item->getRowTotal());
						$object->setBaseSubtotal($object->getBaseSubtotal() + $item->getBaseRowTotal());
						$object->setSubtotalWithDiscount($object->getSubtotalWithDiscount() + ($item->getRowTotal() - $item->getDiscountAmount() + $item->getIncreaseAmount()));
						$object->setBaseSubtotalWithDiscount($object->getBaseSubtotalWithDiscount() + ($item->getBaseRowTotal() - $item->getDiscountAmount() + $item->getIncreaseAmount()));
						$object->setGrandTotal($object->getGrandTotal() + ($item->getRowTotal() - $item->getDiscountAmount() + $item->getIncreaseAmount()));
						$object->setBaseGrandTotal($object->getBaseGrandTotal() + ($item->getBaseRowTotal() - $item->getDiscountAmount() + $item->getIncreaseAmount()));
						$object->setsubtotalInclTax($object->getsubtotalInclTax() + $item->getsubtotalInclTax());
						$object->setBaseSubtoalInclTax($object->getBaseSubtoalInclTax() + $item->getBaseSubtoalInclTax());
						$object->setTotalQty($object->getTotalQty() + $item->getQty());
						$object->setItemQty($object->getItemQty() + 1);
						$object->setGrandTotalAmount($object->getGrandTotal());
						$object->setDiscount($object->getDiscount() + $item->getDiscountAmount());
						$object->setDiscountAmount($object->getDiscountAmount() + $item->getDiscountAmount());
						$object->setIncrease($object->getIncrease() + $item->getIncreaseAmount());
						$object->setIncreaseAmount($object->getIncreaseAmount() + $item->getIncreaseAmount());

						$object->setWeight($object->getWeight() + $item->getWeight());
					}
				}
			}


			if ($totals = $address->getTotals())
			{
				foreach ($totals as $code => $data)
				{
					if ($code != 'grand_total')
					{
						$address->setTotalAmount($code, $quote->getData($code));
					}
				}
			}

			$quote->setSplitBySalesrep($revertSplitBySalesrep);
			$quote->setSalesrep($revertSalesrep);

			return $quote;

		}

		public function partialQuoteValidateMinimumAmount()
		{
			$quote = $this->getQuote();

			$revertSplitBySalesrep = $quote->getSplitBySalesrep();
			$revertSalesrep = $quote->getSalesrep();

			$quote->setSplitBySalesrep(true);

			$return = true;

			foreach (Mage::helper('fvets_salesrep/customer')->getCustomerSalesreps() as $salesrep)
			{
				$quote->setSalesrep($salesrep);
				if (!$quote->validateMinimumAmount())
				{
					$return = false;
				}
			}

			$quote->setSplitBySalesrep($revertSplitBySalesrep);
			$quote->setSalesrep($revertSalesrep);

			return $return;
		}

		public function getQuote()
		{
			return $this->getCheckout()->getQuote();
		}

		public function getCheckout()
		{
			return Mage::getSingleton('checkout/session');
		}
	}

?>