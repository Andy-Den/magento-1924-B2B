<?php
/**
 * Condition list block
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_CheckoutSplit_Block_Payment_Condition_Allpago_Boleto extends FVets_Payment_Block_Condition_Allpago_Boleto
{
	protected function getGrandTotal()
	{
		$value = 0;
		foreach ($this->getQuote()->getAllVisibleItems() as $item){
			$value += $item->getRowTotal() - $item->getDiscountAmount();
		}
		return $value;
	}
}