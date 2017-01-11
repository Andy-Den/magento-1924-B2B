<?php
class FVets_Customer_Block_Account_Navigation	extends Mage_Customer_Block_Account_Navigation
{
	/**
	 * Removes link by name
	 *
	 * @param string $name
	 *
	 * @return Mage_Page_Block_Template_Links
	 */
	public function _construct()
	{
		parent::_construct();

		if (Mage::helper('fvets_customer')->isLoggedUserAttendant()) {
			$this->addLink('change.customer.data', 'customer/account/changecustomerdata', 'Change Customer Data');
		}
	}

	public function removeLinkByName($name)
	{
		if (array_key_exists($name, $this->_links))
			unset($this->_links[$name]);
		return $this;
	}
}