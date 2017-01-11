<?php

class FVets_TablePrice_Model_Sales_Quote_Address_Total_Tableprice extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	public function __construct()
	{
		$this->setCode('fvets_tableprice');
	}

	/**
	 * Get label
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return Mage::helper('fvets_tableprice')->__('Table discount');
	}

	/**
	 * Collect totals process.
	 *
	 * @param Mage_Sales_Model_Quote_Address $address
	 * @return Mage_Sales_Model_Quote_Address_Total_Abstract
	 */
	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);

		$amount = 0;

		foreach ($address->getAllItems() as $item)
		{
			$amount += $item->getTablepriceDiscountAmount();
		}

		$address->setTablepriceDiscountAmount($amount);
		$address->getQuote()->setTablepriceDiscountAmount($amount);

		return $this;
	}

	/**
	 * Fetch (Retrieve data as array)
	 *
	 * @param Mage_Sales_Model_Quote_Address $address
	 * @return array
	 */
	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$amount = $address->getTablepriceDiscountAmount();

		if ($amount!=0) {
			$address->addTotal(array(
				'code' => $this->getCode(),
				'title' => $this->getLabel(),
				'value' => $amount
			));
		}
		return $this;
	}
}