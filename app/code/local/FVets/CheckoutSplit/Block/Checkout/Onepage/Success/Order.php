<?php

class FVets_CheckoutSplit_Block_Checkout_Onepage_Success_Order extends Mage_Core_Block_Template
{

    public function _toHtml()
    {
        $html = '';

        foreach (Mage::getSingleton('checkout/session')->getSalesrepOrdersData() as $order)
        {
            $this->_prepareLastOrder($order['order_id']);
            $this->_prepareLastBillingAgreement($order['billing_agreement_id']);
            $this->_prepareLastRecurringProfiles($order['recurring_profiles_ids']);

            $html .= parent::_toHtml();
        }

        return $html;
    }

	/**
	 * Get last order ID from session, fetch it and check whether it can be viewed, printed etc
	 */
	protected function _prepareLastOrder($orderId)
	{
		if ($orderId) {
			$order = Mage::getModel('sales/order')->load($orderId);
			$this->setOrder($order);
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
				));
			}
		}
	}

	/**
	 * Prepare billing agreement data from an identifier in the session
	 */
	protected function _prepareLastBillingAgreement($agreementId)
	{
		$customerId = Mage::getSingleton('customer/session')->getCustomerId();
		if ($agreementId && $customerId) {
			$agreement = Mage::getModel('sales/billing_agreement')->load($agreementId);
			if ($agreement->getId() && $customerId == $agreement->getCustomerId()) {
				$this->addData(array(
					'agreement_ref_id' => $agreement->getReferenceId(),
					'agreement_url' => $this->getUrl('sales/billing_agreement/view',
						array('agreement' => $agreementId)
					),
				));
			}
		}
	}

	/**
	 * Prepare recurring payment profiles from the session
	 */
	protected function _prepareLastRecurringProfiles($profileIds)
	{
		if ($profileIds && is_array($profileIds)) {
			$collection = Mage::getModel('sales/recurring_profile')->getCollection()
				->addFieldToFilter('profile_id', array('in' => $profileIds))
			;
			$profiles = array();
			foreach ($collection as $profile) {
				$profiles[] = $profile;
			}
			if ($profiles) {
				$this->setRecurringProfiles($profiles);
				if (Mage::getSingleton('customer/session')->isLoggedIn()) {
					$this->setCanViewProfiles(true);
				}
			}
		}
	}

}