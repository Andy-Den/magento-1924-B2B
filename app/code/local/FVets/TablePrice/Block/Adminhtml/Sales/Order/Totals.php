<?php
/**
 * Created by PhpStorm.
 * User: ianitsky
 * Date: 28/05/15
 * Time: 17:29
 */


class FVets_Tableprice_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{
	/**
	 * Initialize order totals array
	 *
	 * @return Mage_Sales_Block_Order_Totals
	 */
	protected function _initTotals()
	{
		parent::_initTotals();
		$amount = $this->getSource()->getTablepriceDiscountAmount();
		if ($amount) {
			$this->addTotalBefore(new Varien_Object(array(
				'code'      => 'fvets_tableprice',
				'value'     => $amount,
				'base_value'=> $amount,
				'label'     => $this->helper('fvets_tableprice')->__('Table discount'),
			), array('nominal', 'subtotal')));
		}

		return $this;
	}

}