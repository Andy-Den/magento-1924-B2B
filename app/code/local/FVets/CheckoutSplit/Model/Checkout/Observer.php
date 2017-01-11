<?php

class FVets_CheckoutSplit_Model_Checkout_Observer
{
	function readdQuoteProducts($observer)
	{
		$session = Mage::getSingleton('checkout/session');
		$quote = $session->getQuote();
		if ($items = $session->getReaddProducts())
		{
			$cart = Mage::getModel('checkout/cart');
			$cart->init();
			foreach ($items as $item)
			{
				if ($item)
				{
					$item = Mage::getModel('sales/quote_item')->load($item);
					$item->setId(null)
						->setQuote($quote)
					;
					//$quote->addItem($item);

					$cart->addProduct($item->getProduct(), $item->getData());
				}
			}
			$cart->save();
			$session->setCartWasUpdated(true);
			/*$quote->setTotalsCollectedFlag(false)->collectTotals();
			$quote->setCustomerId(Mage::getModel('customer/session')->getCustomer()->getId());
			$quote->save();*/
		}
	}

	function readdQuoteProducts2($observer)
	{
		Mage::helper('fvets_checkoutsplit/checkout')->readdQuoteProducts();
	}

	function clearCheckoutSession($observer)
	{
		$checkout = Mage::getSingleton('checkout/session');
		$checkout->unsetData('salesrep_orders_data');
		$checkout->unsetData('readd_products');
		$checkout->unsetData('is_split_checkout');
		$checkout->unsetData('readd_products');
	}

	//No checkout há uma flag para validar se o
	function updateProductsRemoveCheckoutFlag($observer)
	{
		$product = $observer->getProduct();
		if (!isset($product) && $observer->getItem() !== null) {
			$product = $observer->getItem()->getProduct();
		}
		//checkout_cart_add_product_complete
		//Remove a validação de status do allowCheckout, para que o representante não possa fechar o pedido sem validar;
		$salesrep = Mage::helper('fvets_salesrep')->getSalesrepByCustomerAndProduct(Mage::getSingleton('customer/session')->getCustomer(), $product);
		Mage::getSingleton('checkout/session')->{"setSalesrep".$salesrep->getId().'AllowCheckout'}(false);
	}
}