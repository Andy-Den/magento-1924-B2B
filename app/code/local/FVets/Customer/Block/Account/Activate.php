<?php

class FVets_Customer_Block_Account_Activate extends Mage_Core_Block_Template
{

	public function getCustomer() {
		return Mage::getModel('customer/customer')->load($this->getCustomerId());
	}

}
