<?php
class FVets_CheckoutSplit_Helper_Checkout extends Mage_Core_Helper_Abstract
{
	public function readdQuoteProducts()
	{
		$session = Mage::getSingleton('checkout/session');
		if ($items = $session->getReaddProducts())
		{
			$quote = $session->getQuote();
			//Desabilitar o quote
			$quote->setIsActive(false);
			$quote->save();
			//Criar um novo quote
			$quote->setId(null);
			$quote->setIsActive(true);
			$quote->save();
			$cart = Mage::getModel('checkout/cart');
			$cart->init();

			/*==============================*/
			//Remover novos items que o novo quote salva no banco.
			//Fazer com a collection do magento estava dando erro
			/*$removeItems = Mage::getModel('sales/quote_item')->getCollection()
				->addFieldToFilter('quote_id', $quote->getId());
			foreach ($removeItems as $item)
			{
				$item->delete();
			}*/
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$writeConnection = $resource->getConnection('core_write');
			$table = $resource->getTableName('sales/quote_item');

			$itemIds = $readConnection->fetchCol('SELECT item_id FROM '. $table .' where quote_id = ' . $quote->getId());

			foreach($itemIds as $id)
			{
				if (!in_array($id, $items))
				{
					$query = 'DELETE FROM '.$table.' WHERE `item_id`= ' . $id;
					$writeConnection->query($query);
				}
			}
			/*==============================*/

			//Adicionar os itens restantes ao quote
			foreach ($items as $item)
			{
				if ($item)
				{
					$item = Mage::getModel('sales/quote_item')->load($item);

					$item->setQuote($quote);
					$item->save();
					//$quote->addItem($item);
					//$cart->addProduct($item->getProduct(), $item->getData());
				}
			}
			$cart->save();
			$session->setCartWasUpdated(true);
			/*$quote->setTotalsCollectedFlag(false)->collectTotals();
			$quote->setCustomerId(Mage::getModel('customer/session')->getCustomer()->getId());
			$quote->save();*/
		}
	}
}