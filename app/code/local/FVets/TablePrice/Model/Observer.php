<?php

class FVets_TablePrice_Model_Observer
{

	/**
	 * Adds the product tableprice when an item was added to the quote
	 * @param $observer | Varien_Event_Observer
	 */
	public function setOrderItemTableprice($observer)
	{
		$item = $observer->getItem();
		if ($tableprice = $item->getTablepriceObject())
		{
			//Set item price
			if ($tableprice->getDiscount() > 0)
			{

				$originalPrice = 0;

				if ($item->dataHasChangedFor('custom_price'))
				{
					$originalPrice = $item->getOriginalCustomPrice();
				}
				else
				{
					$originalPrice = $item->getOriginalPrice();
				}

				$discountAmount = ($tableprice->getDiscount() * $originalPrice) / 100;
				$specialPrice = $originalPrice - $discountAmount;

				$md5 = md5($specialPrice.$item->getQty());

				if ($md5 != $item->getTablepriceDiscountMd5())
				{
					$item->setTablepriceDiscountMd5($md5);

					//Adiciona um preço "custom" para o produto
					$item->setCustomPrice($specialPrice);
					$item->setOriginalCustomPrice($specialPrice);
					$item->getProduct()->setIsSuperMode(true);

					if($item->getOriginalPrice() > $specialPrice)
					{
						$realDiscount = (($item->getOriginalPrice() - number_format($specialPrice, 2)) * $item->getQty());
					}
					else
					{
						$realDiscount =  0;
					}

					$item->setTablepriceDiscountPercent($tableprice->getDiscount());
					$item->setTablepriceDiscountAmount($realDiscount);
				}
			}
		}
	}

	public function setCheckoutCartProductAddTableprice($observer)
	{
		$item = $observer->getQuoteItem();
		//if (!$item->getTableprice())
		//{
			$tableprice = Mage::helper('fvets_tableprice')->getTablepriceByCustomerAndProduct(Mage::getSingleton('customer/session')->getCustomer(), $item->getProduct());
			if (is_object($tableprice) && $tableprice->getId())
			{
				//Set item tableprice
				$item->setTableprice($tableprice->getIdErp());
				$item->setTablepriceObject($tableprice);
			}
			else
			{
				//Set item tableprice if get from customer group
				$item->setTableprice($tableprice);
			}
		//}
	}
}