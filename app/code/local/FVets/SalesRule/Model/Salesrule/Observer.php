<?php

class FVets_SalesRule_Model_Salesrule_Observer
{
	public function stopConditionDiscount($observer)
	{
		$rule = $observer->getRule();

		if ($rule->getStopConditionDiscount())
		{
			Mage::getSingleton('checkout/session')->getQuote()->setStopConditionDiscount(true);
		}
	}

	public function newsletterSendCoupon($observer)
	{
		if(!Mage::getStoreConfig('fvets_salesrule/newsletter/random_coupon_send_active')) {
			return;
		}

		$customerEmail = $observer->getSubscriber()->getSubscriberEmail();
		$couponCode = $random = substr( md5(rand()), 0, 7);
		$emails = array($customerEmail);
		$storeId = Mage::app()->getStore()->getId();

		$translate = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);

		Mage::getModel('core/email_template')
				->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
				->sendTransactional(
						Mage::getStoreConfig('fvets_salesrule/newsletter/random_coupon_send_email_template'),
						'general',
						array_merge($emails, explode(',', Mage::getStoreConfig('fvets_salesrule/newsletter/random_coupon_send_repository'))),
						null,
						array('customerEmail' => $customerEmail, 'couponCode' => $couponCode));

		$translate->setTranslateInline(true);
		return;
	}
}