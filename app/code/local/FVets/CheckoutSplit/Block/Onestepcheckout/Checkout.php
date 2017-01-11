<?php

class FVets_CheckoutSplit_Block_Onestepcheckout_Checkout extends Idev_OneStepCheckout_Block_Checkout
{
	protected $salesrepTotals = array();

	public function _construct()
	{
		/* Variables to use in phtml */
		$this->getCheckout()->setDefaultShippingMethod($this->_getDefaultShippingMethod());
		$this->getCheckout()->setDifferentShippingAvailable($this->differentShippingAvailable());

		parent::_construct();
	}

	public function setIsSplitCheckout($value = true)
	{
		//Validação para saber se é um checkout de split ou não.
		//Para evitar que faça tarefas desnecessárias.
		$this->getCheckout()->setIsSplitCheckout((bool)$value);
	}

	public function getCheckoutSplitHtml()
	{
		//Se não houver pedidos para serem processados, ou se algum dos pedidos não foi validado,
		//Manda o cliente para o carrinho para fazer o que precisa ser feito
		$this->setBlockMessageId('message_' . uniqid());

		$this->getQuote()->setSplitBySalesrep(true);
		//Para fazer os totais finais, é necessário que tenha um array com os representantes que serão usados.
		$html = '';

		$salesrepToFinalTotals = array();

		$totalSplitOrdersAmmount = 0;

		foreach (Mage::helper('fvets_salesrep/customer')->getCustomerSalesreps() as $salesrep)
		{
			$this->getQuote()->setSalesrep($salesrep);

			Mage::helper('fvets_checkoutsplit')->collectTotals($salesrep);

			if (count($this->getQuote()->getAllVisibleItems()))
			{
				$html .= $this->getChildHtml('checkout_split', false);

				if (!$this->getQuote()->validateMinimumAmount() && !$this->getCheckout()->{"getSalesrep".$salesrep->getId().'AllowCheckout'}())
				{
					$this->setReturnToCart(true);
					continue;
				}
				else
				{
					$this->orderNumber++;
					$this->getCheckout()->{"setSalesrep".$this->getQuote()->getSalesrep()->getId().'Number'}($this->orderNumber);
				}
			}

			$salesrepToFinalTotals[$salesrep->getId()] = $salesrep;

			if ($this->getQuote() && $this->getQuote()->getGrandTotal()) {
				$totalSplitOrdersAmmount += $this->getQuote()->getGrandTotal();
			}
		}
		Mage::getSingleton('checkout/session')->setTotalSplitOrdersAmmount($totalSplitOrdersAmmount);

		$this->getQuote()->setSplitBySalesrep(false);

		Mage::helper('fvets_checkoutsplit')->collectTotals($salesrepToFinalTotals);
		$this->setSalesrepToFinalTotals($salesrepToFinalTotals);

		if ($this->getReturnToCart())
		{
			Mage::app()->getFrontController()->getResponse()->setRedirect('/checkout/cart/#rep-'.$salesrep->getId());
		}

		return $html;

	}

	public function _handlePostData()
	{
		if (!$this->getCheckout()->getIsSplitCheckout())
		{
			return parent::_handlePostData();
		}

		if ($this->getRequest()->getPost() && !$this->getRequest()->getPost('ignore_repost_order'))
		{
			$quote = $this->getQuote();
			$originalQuoteItems = $quote->getAllItems();
			$payment = $this->getRequest()->getPost('payment', false);
			$fvets_quote_conditions = $this->getRequest()->getPost('fvets_quote_conditions', false);
			$onestepcheckout_conditions = $this->getRequest()->getPost('onestepcheckout_conditions', false);
			$salesrep = $this->getRequest()->getPost('salesrep', false);

			if (!$this->getRequest()->getPost('shipping_method', false))
				$this->getRequest()->setPost('shipping_method', 'freeshipping_freeshipping');


			$salesrepOrdersData = $this->getCheckout()->getSalesrepOrdersData();
			if (!is_array($salesrepOrdersData))
			{
				$salesrepOrdersData = array();
			}
			$readdProductsArray = array();

			$salesrep = Mage::getModel('fvets_salesrep/salesrep')->load($salesrep);
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
			$salesrepItems = Mage::helper('fvets_salesrep/quote')->getSalesrepItems($originalQuoteItems, true);

			if (count($salesrepItems))
			{
				foreach ($salesrepItems as $item) {
					$item->setId(null);
					$quote->addItem($item);
				}
			}

			//Adiciona dados da compra para cada pedido
			if ($payment)
				$this->getRequest()->setPost('payment', str_replace('-'.$salesrep->getId(), '', $payment[$salesrep->getId()]));
			if ($fvets_quote_conditions)
				$this->getRequest()->setPost('fvets_quote_conditions', $fvets_quote_conditions[$salesrep->getId()]);
			if ($onestepcheckout_conditions)
				$this->getRequest()->setPost('onestepcheckout_conditions', $onestepcheckout_conditions[$salesrep->getId()]);

			//Refaz o total para o representante
			//$quote->setTotalsCollectedFlag(false)->collectTotals();
			Mage::helper('fvets_checkoutsplit')->collectTotals($salesrep, $quote);
			Mage::dispatchEvent('sales_quote_collect_totals_after', array('quote' => $quote));

			//Deixa o parent salvar a order do magento, como sempre foi feito.
			parent::_handlePostData();

			if(!$this->hasFormErrors()) {

				//Salva dados de sessão da order para usar no front de sucesso.
				$salesrepOrdersData[$salesrep->getId()] = array(
					'order_id' => $this->getCheckout()->getLastOrderId(),
					'order_increment' => $this->getCheckout()->getLastRealOrderId(),
					'billing_agreement_id' => $this->getCheckout()->getLastBillingAgreementId(),
					'recurring_profiles_ids' => $this->getCheckout()->getLastRecurringProfileIds(),
				);


				$this->getRequest()->setPost('ignore_repost_order', 'true');

				$this->getCheckout()->setSalesrepOrdersData($salesrepOrdersData);

				foreach (Mage::helper('fvets_salesrep/customer')->getCustomerSalesreps() as $salesrep)
				{
					if ($salesrep->getId() != $this->getRequest()->getPost('salesrep', false))
					{
						//Readiciona os produtos no carrinho quando completar os pedidos.
						//$readdProductsArray = array_merge($readdProductsArray, $salesrepItems);
						$quote->setSalesrep($salesrep);
						$salesrepItems = Mage::helper('fvets_salesrep/quote')->getSalesrepItems($originalQuoteItems, true);
						foreach ($salesrepItems as $item)
						{
							$readdProductsArray[] = $item->getId();
						}
					}
				}

				$this->getCheckout()->setReaddProducts($readdProductsArray);

				Mage::dispatchEvent('checkoutsplit_after_place_order');

				$this->_finalSaveOrder();
			}
			else
			{
				//Limpa e readiciona os produtos originais na quote para que não sejam duplicados
				foreach ($originalQuoteItems as $item)
				{
					$readdProductsArray[] = $item->getId();
				}

				$this->getCheckout()->setReaddProducts($readdProductsArray);
				Mage::helper('fvets_checkoutsplit/checkout')->readdQuoteProducts();

				//Não deixa fazer o repost do pedido
				$this->getRequest()->setPost('ignore_repost_order', 'true');

				//Se os termos não estão definidos, retorna o pedido para que seja reavaliado.
				Mage::app()->getFrontController()->getResponse()->setRedirect($this->getUrl('checkoutsplit/ajax/get_checkout_first_step', array('_secure'=>true,'salesrep'=>$salesrep->getId(), 'error' => serialize($this->formErrors))));
				$this->getResponse();
			}
		}
	}

	protected function afterPlaceOrder()
	{
		if ($this->getCheckout()->getIsSplitCheckout())
		{
			//Redirecionar para o lugar correto.
			$this->getOnepage()->getCheckout()->setRedirectUrl($this->getUrl('checkoutsplit/ajax/success', array('_secure'=>true)));
		}

		parent::afterPlaceOrder();
	}

	public function _finalSaveOrder()
	{
		return $this->getResponse();
	}

	public function addSalesrepToFinalTotals($data)
	{
		$this->_finalTotals[] = $data;
	}


}