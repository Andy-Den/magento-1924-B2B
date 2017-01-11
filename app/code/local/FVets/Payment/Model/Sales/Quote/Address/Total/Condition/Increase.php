<?php

class FVets_Payment_Model_Sales_Quote_Address_Total_Condition_Increase extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	public function __construct()
	{
		$this->setCode('payment_condition_increase');
	}

	/**
	 * Fetch (Retrieve data as array)
	 *
	 * @param Mage_Sales_Model_Quote_Address $address
	 * @return array
	 */
	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$condition = $address->getIncreaseType($this->getCode());
		if (isset($condition) && $condition['value'])
		{
			$address->addTotal(array(
				'code'=> $this->getCode(),
				'title'=>$condition['title'],
				'value'=>$condition['value']
			));
		}

		return $this;
	}
}