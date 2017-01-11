<?php

class FVets_CheckoutSplit_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{

	/*public function saveOrder()
	{
		$quote = $this->getQuote();
		$originalQuoteItems = $quote->getAllItems();

		$salesrepOrdersData = array();

		foreach (Mage::helper('fvets_salesrep/customer')->getCustomerSalesreps() as $salesrep)
		{
			$quote->setItemsQty(0);
			$quote->setItemsCount(0);
			$quote->setItemsQtys(null);
			$quote->setSplitBySalesrep(true);
			$quote->setSalesrep($salesrep);

			//Remover cache de produtos, utilizado pela warehouse.
			foreach($quote->getAllAddresses() as $address) {
				$address->unsetData('cached_items_nominal');
				$address->unsetData('cached_items_nonnominal');
				$address->unsetData('cached_items_all');
			}

			// Limpa a quote
			foreach ($quote->getAllItems() as $item) {
				$quote->getItemsCollection()->removeItemByKey($item->getId());
			}
			// Adiciona somente itens do representante atual
			foreach (Mage::helper('fvets_salesrep/quote')->getSalesrepItems($originalQuoteItems, true) as $item) {
				$item->setId(null);
				$quote->addItem($item);
			}
			//Refaz o total para o representante
			$quote->setTotalsCollectedFlag(false)->collectTotals();

			if (!$quote->validateMinimumAmount())
			{
				continue;
			}

			//Deixa o parent salvar a order do magento, como sempre foi feito.
			parent::saveOrder();

			//Salva dados de sessão da order para usar no front de sucesso.
			$salesrepOrdersData[$salesrep->getId()] = array(
				'order_id' => $this->_checkoutSession->getLastOrderId(),
				'order_increment' => $this->_checkoutSession->getLastRealOrderId(),
				'billing_agreement_id' => $this->_checkoutSession->getLastBillingAgreementId(),
				'recurring_profiles_ids' => $this->_checkoutSession->getLastRecurringProfileIds(),
			);
		}

		$this->_checkoutSession->setSalesrepOrdersData($salesrepOrdersData);

		return $this;
	}*/

}