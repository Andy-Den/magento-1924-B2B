<?php
class Idev_OneStepCheckout_Block_Checkout_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{
    /**
     * Get one page checkout page url
     *
     * @return bool
     */
    public function getCheckoutUrl()
    {
        if (!$this->helper('onestepcheckout')->isRewriteCheckoutLinksEnabled()){
            return parent::getCheckoutUrl();
        }
        return $this->getUrl('onestepcheckout', array('_secure'=>true));
    }

	/**
	 * Get one page checkout page url
	 *
	 * @return bool
	 */
	public function getCartUrl()
	{
		return $this->helper('checkout/cart')->getCartUrl();
	}

	//verifica a real quantidade de itens do carrinho já que o módulo de brindes da amasty não leva em conta os brindes;
	public function getSummaryCount()
	{
		$_items = $this->getItems();
		$totalItens = 0;
		foreach ($_items as $_item)
		{
			$totalItens += $_item->getQty();
		}
		return $totalItens;
	}
}
