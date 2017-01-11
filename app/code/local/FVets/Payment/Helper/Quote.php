<?php

class FVets_TablePrice_Helper_Quote extends Mage_Core_Helper_Abstract
{

	/*public function collectCustomDiscount($quote, $discount)
	{

		$discountAmount = 0;

		if ($quote->getId()) {


			if ($discount > 0) {
				$total = $quote->getBaseSubtotal();
				$quote->setSubtotal(0);
				$quote->setBaseSubtotal(0);

				$quote->setSubtotalWithDiscount(0);
				$quote->setBaseSubtotalWithDiscount(0);

				$quote->setGrandTotal(0);
				$quote->setBaseGrandTotal(0);


				$canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
				foreach ($quote->getAllAddresses() as $address) {

					$address->setSubtotal(0);
					$address->setBaseSubtotal(0);

					$address->setGrandTotal(0);
					$address->setBaseGrandTotal(0);

					$address->collectTotals();

					$quote->setSubtotal((float)$quote->getSubtotal() + $address->getSubtotal());
					$quote->setBaseSubtotal((float)$quote->getBaseSubtotal() + $address->getBaseSubtotal());

					$quote->setSubtotalWithDiscount(
						(float)$quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
					);
					$quote->setBaseSubtotalWithDiscount(
						(float)$quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
					);

					$quote->setGrandTotal((float)$quote->getGrandTotal() + $address->getGrandTotal());
					$quote->setBaseGrandTotal((float)$quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

					$quote->save();

					//Dar a percentagem de desconto
					$discountAmount = ($discount * $total) / 100;

					$quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount)
						->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount)
						->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
						->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
						->save();


					if ($address->getAddressType() == $canAddItems) {
						//echo $address->setDiscountAmount; exit;
						$address->setSubtotalWithDiscount((float)$address->getSubtotalWithDiscount() - $discountAmount);
						$address->setGrandTotal((float)$address->getGrandTotal() - $discountAmount);
						$address->setBaseSubtotalWithDiscount((float)$address->getBaseSubtotalWithDiscount() - $discountAmount);
						$address->setBaseGrandTotal((float)$address->getBaseGrandTotal() - $discountAmount);
						if ($address->getDiscountDescription()) {
							$address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
							$address->setDiscountDescription($address->getDiscountDescription() . ', '.$discount.'% para a condiçao de pagamento');
							$address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
						} else {
							$address->setDiscountAmount(-($discountAmount));
							$address->setDiscountDescription($discount. '% para a condiçao de pagamento');
							$address->setBaseDiscountAmount(-($discountAmount));
						}
						$address->save();
					}//end: if
				} //end: foreach
				//echo $quote->getGrandTotal();

				foreach ($quote->getAllItems() as $item)
				{
					$discountAmount = ($discount * $item->getBaseRowTotal()) / 100;
					$item
						->setDiscountPercent($item->getDiscountPercent() + $discount)
						->setDiscountAmount($item->getDiscountAmount() + $discountAmount)
						->setBaseDiscountAmount($item->getBaseDiscountAmount() + $discountAmount)
						->save();
				}
			}
		}
	}*/

}