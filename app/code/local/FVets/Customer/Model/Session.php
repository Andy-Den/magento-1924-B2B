<?php

class FVets_Customer_Model_Session extends Mage_Customer_Model_Session
{

	/**
	 * Customer authorization
	 *
	 * @param   string $username
	 * @param   string $password
	 * @return  bool
	 */
	public function login($username, $password)
	{

		Mage::dispatchEvent('before_customer_login', array('username' => $username, 'password' => $password));

		/** @var $customer Mage_Customer_Model_Customer */
		$customer = Mage::getModel('customer/customer')
			->setWebsiteId(Mage::app()->getStore()->getWebsiteId());


		if ($customer->authenticate($username, $password)) {
			$this->setCustomerAsLoggedIn($customer);
			$this->renewSession();
			return true;
		}
		
		return false;
	}

	/**
	 * Checking customer login status
	 *
	 * @return bool
	 */
	public function isLoggedIn()
	{
		if ((bool)$this->getId() && (bool)$this->checkCustomerId($this->getId()))
		{
			$storeviews = explode(',',$this->getCustomer()->getStoreView());
			if (is_array($storeviews) && count($storeviews) > 0)
			{
				#biscoito
				if (in_array(Mage::app()->getStore()->getId(), $storeviews) || $this->getCustomer()->getIsAllowedToLogin())
				{
					return true;
				}
				else
				{
					header('Location: '.Mage::app()->getStore($storeviews[0])->getBaseUrl());
					exit;
				}
			}
		}

		return false;
	}

}