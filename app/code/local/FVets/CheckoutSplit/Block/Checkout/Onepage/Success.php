<?php

class FVets_CheckoutSplit_Block_Checkout_Onepage_Success extends Mage_Core_Block_Template
{

	public function _construct()
	{
		parent::_construct();

		$this->_loadConfig();

		$this->_prepareLastOrder();

		$message = Mage::getModel('core/variable')->setStoreId(Mage::app()->getStore()->getId())->loadByCode('checkout_success_message')->getValue();

		if (trim($message) != '') {
			Mage::getSingleton('checkout/session')->addNotice($message);
		}
	}

	protected function _loadConfig()
	{
		$this->settings = Mage::helper('onestepcheckout/checkout')->loadConfig();
	}

	public function getLogo()
	{
		if (empty($this->_data['checkout_logo'])) {
			$this->_data['checkout_logo'] = $this->settings['checkout_logo'];
			$this->_data['checkout_logo'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'logos/' . $this->_data['checkout_logo'];
		}
		return $this->_data['checkout_logo'];
	}

	public function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}

	public function getCheckoutAlertMessage()
	{
		$value = Mage::getModel('core/variable')->setStoreId(Mage::app()->getStore()->getId())->loadByCode('checkout_success_message')->getValue('html');
		return trim($value);
	}

	/**
	 * Get last order ID from session, fetch it and check whether it can be viewed, printed etc
	 */
	protected function _prepareLastOrder()
	{
		$orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
		if ($orderId) {
			$order = Mage::getModel('sales/order')->load($orderId);
			if ($order->getId()) {
				$isVisible = !in_array($order->getState(),
					Mage::getSingleton('sales/order_config')->getInvisibleOnFrontStates());
				$this->addData(array(
					'is_order_visible' => $isVisible,
					'view_order_id' => $this->getUrl('sales/order/view/', array('order_id' => $orderId)),
					'print_url' => $this->getUrl('sales/order/print', array('order_id'=> $orderId)),
					'can_print_order' => $isVisible,
					'can_view_order'  => Mage::getSingleton('customer/session')->isLoggedIn() && $isVisible,
					'order_id'  => $order->getIncrementId(),
					'billing_address' => $order->getBillingAddress(),
				));
			}
		}
	}

}