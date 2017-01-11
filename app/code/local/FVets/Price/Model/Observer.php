<?php

	class FVets_Price_Model_Observer
	{
		/**
		 * @param $observer
		 */
		public function cartAddPercentToProductPrice($observer)
		{
			if (Mage::getStoreConfig('price/general/enabled') && Mage::getStoreConfig('price/general/addon') == 'cart')
			{
				$item = $observer->getItem();

				$percent = Mage::getStoreConfig('price/general/addpercent');

				$originalPrice = $item->getOriginalPrice();

				$amount = ($percent * $originalPrice) / 100;
				$specialPrice = $originalPrice + $amount;

				//$item->setOriginalPrice($item->getOriginalPrice() + $discountAmount);
				//$item->setPrice($item->getPrice() + $discountAmount);
				//$item->setBasePrice($item->getBasePrice() + $discountAmount);

				//Adiciona um preço "custom" para o produto
				$item->setCustomPrice($specialPrice);
				$item->setOriginalCustomPrice($specialPrice);
				$item->getProduct()->setIsSuperMode(true);
			}
		}
	}

?>