<?php
class FVets_CheckoutSplit_Block_Cart_Split extends Mage_Checkout_Block_Cart
{

	private $_customer_salesrep = null;

	public $orderNumber = 0;

	/**
	 * Retrieve block HTML divided by salesrep
	 */
	protected function _toHtml()
	{

		$html = '';

		//Dividimos os blocos por representantes.
		//Então é necessário adicionar essa variável para que dentro do getAllVisibleItems, possam ser pegos somente os itens do representante que desejamos.
		$this->getQuote()->setSplitBySalesrep(true);

		foreach (Mage::helper('fvets_salesrep/customer')->getCustomerSalesreps() as $salesrep)
		{
			$this->setBlockMessageId('message_' . rand(0, time()));
			$this->getQuote()->setSalesrep($salesrep);
			if (count($this->getQuote()->getAllVisibleItems()))
			{
				//Este é um collectTotals fake, que usa os valores dos itens já calculados para poder saber os valores totais.
				Mage::helper('fvets_checkoutsplit')->collectTotals($salesrep);

				//Caso o pedido não esteja no mínimo permitido
				if (!$this->getQuote()->validateMinimumAmount())
				{
					//Adiciona mensagem de erro
					$this->getMessagesBlock()->addNotice(Mage::getStoreConfig('sales/minimum_order/description'));
					if (!Mage::getSingleton('checkout/session')->{"getSalesrep".$salesrep->getId().'AllowCheckout'}())
					{
						$this->setSalesrepAllowCheckout(false);
					}
					else
					{
						$this->setSalesrepAllowCheckout(true);
					}
				}
				else
				{
					$this->setSalesrepAllowCheckout(true);
				}

				$this->orderNumber++;
				$html .= parent::_toHtml();
			}
			$this->getMessagesBlock()->getMessageCollection()->clear();
		}

		//Após terminar a construção do html removemos a variável de split para que não seja feito o split onde não se deve.
		$this->getQuote()->unsetData('split_by_salesrep');
		//Refazemos aqui o collectTotals para retornar o quote aos valores padrões.
		Mage::helper('fvets_checkoutsplit')->collectTotals();

		return $html;
	}

}