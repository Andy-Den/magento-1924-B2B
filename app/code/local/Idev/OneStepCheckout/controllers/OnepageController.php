<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Idev_OneStepCheckout_OnepageController extends Mage_Checkout_OnepageController {

	/**
	 * Predispatch: should set layout area
	 * Substitui o predispach da classe principal para que nao chame o metodo _preDispatchValidateCustomer
	 *
	 * @return Mage_Checkout_OnepageController
	 */
	public function preDispatch()
	{
		call_user_func(array(get_parent_class(get_parent_class($this)), 'preDispatch'));

		if (Mage::getStoreConfig(Idev_OneStepCheckout_Helper_Data::XML_GENERAL_VALIDATE_CHECKOUT_CUSTOMER))
			$this->_preDispatchValidateCustomer();

		$checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
		if ($checkoutSessionQuote->getIsMultiShipping()) {
			$checkoutSessionQuote->setIsMultiShipping(false);
			$checkoutSessionQuote->removeAllAddresses();
		}

		if (!$this->_canShowForUnregisteredUsers()) {
			$this->norouteAction();
			$this->setFlag('',self::FLAG_NO_DISPATCH,true);
			return;
		}

		return $this;
	}

}