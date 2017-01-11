<?php

class FVets_Payment_Model_Condition_Observer
{
	public function setOrderCondition($observer)
	{
		if (!Mage::registry('fvets_quote_conditions'))
		{
			if ($conditions = Mage::app()->getRequest()->getPost('fvets_quote_conditions'))
			{
				if (is_array($conditions))
				{
					$conditions= $conditions[Mage::app()->getRequest()->getPost('salesrep')];
				}

				Mage::getSingleton('checkout/session')->setFvetsQuoteConditions($conditions);

				$condition = Mage::getModel('fvets_payment/condition')->load($conditions);
				Mage::getSingleton('checkout/session')->setPaymentConditionSplit($condition->getSplit());
				Mage::register('fvets_quote_conditions', true);
			}
		}
	}

	public function unsetCondition($observer)
	{
		Mage::getSingleton('checkout/session')->setFvetsQuoteConditions(null);
	}

	public function setConditionDiscount($observer)
	{
		$condition = Mage::getSingleton('checkout/session')->getFvetsQuoteConditions();
		if ($condition && !is_array($condition)) {
			//Pega a condiçao selecionada
			$condition = Mage::getModel('fvets_payment/condition')->load($condition);

			$quote = $observer->getEvent()->getQuote();
			$quoteid = $quote->getId();
			$conditionDiscount = Mage::app()->getWebsite()->getConfig('payment_condition/general/condition_discount_after_subtotal_calculed_items');
			$discountAmount = 0;
			$discountPercent = $condition->getDiscount();

			if ($quoteid) {

				if ($discountPercent > 0) {
					$total = $quote->getBaseSubtotal();

					if($conditionDiscount != 1) {
						$total = $quote->getBaseSubtotal();	
					}

					$canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
					foreach ($quote->getAllAddresses() as $address)
					{
						//Dar a percentagem de desconto
						$discountAmount = ($discountPercent * $total) / 100;

						$quote->setGrandTotal($total - $discountAmount)
							->setBaseGrandTotal($total - $discountAmount)
							->setSubtotalWithDiscount($total - $discountAmount)
							->setBaseSubtotalWithDiscount($total - $discountAmount)
							//->save()
						;


						if ($address->getAddressType() == $canAddItems) {
							//echo $address->setDiscountAmount; exit;
							$address->setSubtotalWithDiscount((float)$address->getSubtotalWithDiscount() - $discountAmount);
							$address->setGrandTotal((float)$address->getGrandTotal() - $discountAmount);
							$address->setBaseSubtotalWithDiscount((float)$address->getBaseSubtotalWithDiscount() - $discountAmount);
							$address->setBaseGrandTotal((float)$address->getBaseGrandTotal() - $discountAmount);

							if ($address->getDiscountDescription()) {
								$address->setDiscountDescription($address->getDiscountDescription() . ', '.$discountPercent.'% para a condição de pagamento');
							} elseif ($address->getDiscountAmount()) {
								$address->setDiscountDescription($discountPercent. '% para a condição de pagamento, outros');
							} else {
								$address->setDiscountDescription($discountPercent. '% para a condição de pagamento');
							}

							$address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
							$address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));

							//Adicionar tipo de desconto, para criar o total
							$address->addDiscountType('payment_condition_discount',
								array(
									'code' => 'payment_condition_discount',
									'title' => 'Desconto condiçao de pagamento',
									'percent' => $discountPercent,
									'value' => $discountAmount
								)
							);

							//$address->save();
						}//end: if
					} //end: foreach
					//echo $quote->getGrandTotal();

					/**
					 * @var Innoexts_Warehouse_Model_Sales_Quote_Item $item
					 */
					foreach ($quote->getAllItems() as $item) {
						$discountAmount = ($discountPercent * $item->getBaseRowTotal()) / 100;
						if($conditionDiscount == 1) {
							$discountAmounted = $item->getBaseRowTotal() - $item->getDiscountAmount();
							$discountAmount = ($discountPercent * $discountAmounted) / 100;
						}

						$item->setDiscountPercent($item->getDiscountPercent() + $discountPercent)
							->setDiscountAmount($item->getDiscountAmount() + $discountAmount)
							->setBaseDiscountAmount($item->getBaseDiscountAmount() + $discountAmount);
					}

					$observer->getQuote()->setPaymentDiscount($discountPercent);
				}
			}
		}
	}

	public function setConditionIncrease($observer)
	{
		$condition = Mage::getSingleton('checkout/session')->getFvetsQuoteConditions();
		if ($condition && !is_array($condition)) {
			//Pega a condiçao selecionada
			$condition = Mage::getModel('fvets_payment/condition')->load($condition);

			$quote = $observer->getEvent()->getQuote();
			$quoteid = $quote->getId();

			$increaseAmount = 0;
			$increasePercent = $condition->getIncrease();

			if ($quoteid) {


				if ($increasePercent > 0) {
					$total = $quote->getBaseSubtotal();

					$configIncreaseAfterSubtotalCalculated = Mage::app()->getWebsite()
						->getConfig('payment_condition/general/condition_increase_after_subtotal_calculated_items');

					// just one of two configs needs be checked!
					$configCalculateItemsOnConditionPaymentIncreaseCalculated = Mage::app()->getWebsite()
						->getConfig('payment_condition/general/calculate_items_on_condition_payment_increase_calculated');

					if ($configCalculateItemsOnConditionPaymentIncreaseCalculated == 1) {
						$total = $quote->getBaseSubtotal();
						/**
						 * @var Innoexts_Warehouse_Model_Sales_Quote_Item $item
						 */
						$totalDiscountAmount = 0;
						$totalIncreaseAmount = 0;
						foreach ($quote->getAllItems() as $item) {
							$increaseAmount = ($increasePercent * $item->getBaseRowTotal()) / 100;
							if ($configIncreaseAfterSubtotalCalculated == 1) {
								$discountAmounted = $item->getBaseRowTotal() - $item->getDiscountAmount();
								$increaseAmount = ($increasePercent * $discountAmounted) / 100;
							}

							if ($configCalculateItemsOnConditionPaymentIncreaseCalculated == 1) {
								$discountPercent = $item->getDiscountPercent();
								$increaseAmount = ($increasePercent * $item->getPrice()) / 100;
								$discountAmount = ($discountPercent * ($item->getPrice() + $increaseAmount)) / 100;

								$increaseAmount = round($increaseAmount, 2) * $item->getQty();
								$discountAmount = round($discountAmount, 2) * $item->getQty();

								$item->setDiscountAmount($discountAmount)
									->setBaseDiscountAmount($discountAmount);

								$totalDiscountAmount += $discountAmount;
								$totalIncreaseAmount += $increaseAmount;
							}

							$item->setIncreasePercent($increasePercent)
								->setIncreaseAmount($increaseAmount)
								->setBaseIncreaseAmount($increaseAmount);
						}
					} else {
						$totalIncreaseAmount = ($total * $increasePercent) / 100;
					}

					$canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
					$increaseAmount = $totalIncreaseAmount;
					foreach ($quote->getAllAddresses() as $address)
					{
						//Dar a percentagem de acréscimo
						$quote->setGrandTotal($total + $increaseAmount)
							->setBaseGrandTotal($total + $increaseAmount)
							->setSubtotalWithIncrease($total + $increaseAmount)
							->setBaseSubtotalWithIncrease($total + $increaseAmount)
						;


						if ($address->getAddressType() == $canAddItems) {
							$address->setSubtotalWithIncrease((float)$address->getSubtotalWithIncrease() + $increaseAmount);
							$address->setGrandTotal((float)$address->getGrandTotal() + $increaseAmount);
							$address->setBaseSubtotalWithIncrease((float)$address->getBaseSubtotalWithIncrease() + $increaseAmount);
							$address->setBaseGrandTotal((float)$address->getBaseGrandTotal() + $increaseAmount);

							if ($address->getIncreaseDescription()) {
								$address->setIncreaseDescription($address->getIncreaseDescription() . ', '.$increasePercent.'% para a condição de pagamento');
							} elseif ($address->getIncreaseAmount()) {
								$address->setIncreaseDescription($increasePercent. '% para a condição de pagamento, outros');
							} else {
								$address->setIncreaseDescription($increasePercent. '% para a condição de pagamento');
							}

							$address->setIncreaseAmount($increaseAmount);
							$address->setBaseIncreaseAmount($increaseAmount);

							//Adicionar tipo de desconto, para criar o total
							$title = Mage::getStoreConfig('payment_condition/general/increase_text');
							if(!$title) {
								$title = 'Acréscimo da condiçao de pagamento';
							}
							$address->addIncreaseType('payment_condition_increase',
								array(
									'code' => 'payment_condition_increase',
									'title' => $title,
									'percent' => $increasePercent,
									'value' => $increaseAmount
								)
							);

							$observer->getQuote()->setPaymentIncrease($increasePercent);
							$observer->getQuote()->setPaymentIncreaseAmount($increaseAmount);
						}//end: if
					} //end: foreach

					if (1 == $configCalculateItemsOnConditionPaymentIncreaseCalculated) {
						$total = $quote->getGrandTotal() - $totalDiscountAmount;

						$quote->setGrandTotal($total)
							->setBaseGrandTotal($total)
							->setSubtotalWithDiscount($total)
							->setBaseSubtotalWithDiscount($total);

						foreach ($quote->getAllAddresses() as $address) {
							if ($address->getAddressType() == $canAddItems && $totalDiscountAmount > 0) {
								$address->setDiscountAmount($totalDiscountAmount);
								$address->setBaseDiscountAmount($totalDiscountAmount);
								$address->setGrandTotal($total);
								$address->setBaseGrandTotal($total);
								$address->setGrandTotalWithDiscount($total);
								$address->setBaseGrandTotalWithDiscount($total);

								$address->addDiscountType('discount_' . Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION,
									array_merge(
										$address->getDiscountType('discount_' . Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION),
										['value' => $totalDiscountAmount]
									)
								);
							}
						}
					}

					foreach ($quote->getAllItems() as $item)
					{
						$increaseAmount = ($increasePercent * $item->getBaseRowTotal()) / 100;
						$item
							->setIncreasePercent($increasePercent)
							->setIncreaseAmount($increaseAmount)
							->setBaseIncreaseAmount($increaseAmount)
							//->save()
						;
					}
				}
			}
		}
	}

	public function cleanConditions($observer) {
		$this->unsetCondition($observer);
		$this->removeConditionIncrease($observer);
	}

	public function removeConditionIncrease($observer)
	{
		$quote = $observer->getEvent()->getQuote();
		foreach ($quote->getAllItems() as $item)
		{
			$item
				->setIncreasePercent(0)
				->setIncreaseAmount(0)
				->setBaseIncreaseAmount(0)
				//->save()
			;
		}
		$quote->setPaymentIncrease(null);
		$quote->setPaymentIncreaseAmount(null);
	}
}

?>