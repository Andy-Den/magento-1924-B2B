<?php

	class FVets_Salesrep_Login_IndexController extends Mage_Core_Controller_Front_Action
	{
		public function indexAction()
		{
			$customerId   = $this->getRequest()->getParam('id');
			$customer     = Mage::getModel('customer/customer')->load($customerId);

			$transport = new Varien_Object(array('disable' => false));
			Mage::dispatchEvent('widgentologin_disable', array(
				'transport'   => $transport,
				'customer_id' => $customerId,
			));

			$hash  = md5(uniqid(mt_rand(), true));
			try {
				$login = Mage::getModel('widgentologin/login')
					->setLoginHash($hash)
					->setCustomerId($customerId)
					->setSalesrepId(Mage::getSingleton('fvets_salesrep/session')->getCustomer()->getId())
					->setCreatedAt(now())
					->setIsActive(1)
					->save();
			} catch(Exception $e)
			{

			}

			return $this->_redirect('widgentologin/', array(
				'id'     => $hash,
				'_store' => Mage::app()->getStore()->getCode(),
			));
		}
	}

?>