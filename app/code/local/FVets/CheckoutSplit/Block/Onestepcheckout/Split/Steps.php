<?php

class FVets_CheckoutSplit_Block_Onestepcheckout_Split_Steps extends FVets_CheckoutSplit_Block_Onestepcheckout_Checkout
{
	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (!$this->getTemplate()) {
			return '';
		}
		$this->collectTotals();
		$html = $this->renderView();
		$html .= $this->jkfhjhgfdgfdg();
		return $html;
	}

	public function setSplitSalesrep($salesrep)
	{
		if (!$salesrep instanceof FVets_Salesrep_Model_Salesrep)
		{
			$salesrep = Mage::getModel('fvets_salesrep/salesrep')->load($salesrep);
		}

		$this->getQuote()->setSalesrep($salesrep);
	}

	public function collectTotals()
	{
		$salesrep = $this->getQuote()->getSalesrep();

		$this->getQuote()->setSplitBySalesrep(true);

		Mage::helper('fvets_checkoutsplit')->collectTotals($salesrep);

		$this->salesrepTotals[] = array($salesrep->getData(), $this->getQuote()->getData());

		//Mage::dispatchEvent('fvets_checkoutsplit_collect_discount', array('quote' =>  $this->getQuote()));

		//A partir deste momento não preciso mais que a "Amasty Promo" revise quais itens devem ser adicionados ou não no carrinho.
		//Seto essa informação para que não haja mais iteração.
		Mage::getSingleton('checkout/session')->setAddNewProduct(false);
	}

	public function getIsDisabled($salesrep)
	{
		if ($salesrep instanceof FVets_Salesrep_Model_Salesrep)
		{
			$salesrep = $salesrep->getId();
		}

		return $this->getCheckout()->{"getSalesrep".$salesrep.'DisableCheckout'}() || !$this->getQuote()->validateMinimumAmount();
	}

	public function getOrderNumber()
	{
		return $this->getCheckout()->{"getSalesrep".$this->getQuote()->getSalesrep()->getId().'Number'}();
	}

	public function jkfhjhgfdgfdg()
	{
		return
		'<script>
			jQuery(".grand-totals-salesrep-'.$this->getQuote()->getSalesrep()->getId().'").remove();
			jQuery("#checkout-header-totals").append(\'<div class="grand-totals-salesrep-'.$this->getQuote()->getSalesrep()->getId().' grand-totals-salesrep-'.$this->getQuote()->getSalesrep()->getId().'-shared grand-totals-salesrep-shared">'.$this->getOrderNumber().'º Pedido ('.explode(' ', $this->getQuote()->getSalesrep()->getName())[0].') '.stripslashes($this->helper('checkout')->formatPrice($this->getQuote()->getGrandTotal())).'</div>\');
			sumSalesrepValues('.$this->getQuote()->getSalesrep()->getId().', '.$this->getQuote()->getGrandTotal().');
		</script>';
	}
}