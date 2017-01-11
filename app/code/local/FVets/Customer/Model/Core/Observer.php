<?php

class FVets_Customer_Model_Core_Observer
{
	public function addCustomerTipopessoaHandle($observer)
	{
		$customerSession = Mage::getSingleton('customer/session');
		if ($customerSession->isLoggedIn())
		{
			$layout = Mage::getSingleton('core/layout');
			$layout->getUpdate()->addHandle('customer_tipopessoa_'.$customerSession->getCustomer()->getTipopessoa());
		}
	}
}