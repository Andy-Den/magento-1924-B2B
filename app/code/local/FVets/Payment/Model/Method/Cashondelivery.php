<?php
/**
 * Cash on delivery payment method model
 */
class FVets_Payment_Model_Method_Cashondelivery extends Mage_Payment_Model_Method_Cashondelivery
{

	public function assignData($data)
	{
		$this->getInfoInstance()->setPaymentConditions(Mage::app()->getRequest()->getParam('payment_conditions'));
		parent::assignData($data);
	}

	public function order(Varien_Object $payment, $amount)
	{

		$paymentConditions = Mage::app()->getRequest()->getParam('payment_conditions');

		if ($paymentConditions && !empty($paymentConditions)) {
			$additionalData = $payment->getAdditionalData();
			if ($additionalData)
				$additionalData = unserialize($additionalData);
			else
				$additionalData = array();

			$additionalData['payment_conditions'] = $paymentConditions;

			$payment->setAdditionalData(serialize($additionalData));
			$payment->save();
		}

		return $this;
	}
}