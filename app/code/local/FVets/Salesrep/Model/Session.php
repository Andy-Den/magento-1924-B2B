<?php

	class FVets_Salesrep_Model_Session extends Mage_Customer_Model_Session
	{
		public function __construct()
		{
			$namespace = 'salesrep';
			if ($this->getCustomerConfigShare()->isWebsiteScope()) {
				$namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());
			}

			$this->init($namespace);
			Mage::dispatchEvent('salesrep_session_init', array('salesrep_session'=>$this));
		}

		/**
		 * Logout customer
		 *
		 * @return Mage_Customer_Model_Session
		 */
		public function logout()
		{
			if ($this->isLoggedIn()) {
				Mage::dispatchEvent('salesrep_logout', array('customer' => $this->getCustomer()) );
				$this->_logout();
			}
			return $this;
		}
	}