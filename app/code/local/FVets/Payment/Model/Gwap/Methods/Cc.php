<?php

class FVets_Payment_Model_Gwap_Methods_Cc extends  Allpago_Gwap_Model_Methods_Cc
{

	/**
	 * Validate Autorize
	 *
	 * @param Varien_Object $payment
	 * @param float $amount
	 * @return $this|Mage_Payment_Model_Abstract
	 * @throws Exception
	 */
	public function validateAutorize(Varien_Object $payment, $amount)
	{
		$gwap = Mage::getModel('gwap/order')->load($payment->getOrder()->getId(), 'order_id');
		$data = new Varien_Object(unserialize(Mage::helper('core')->decrypt($gwap->getInfo())));
		$order = $payment->getOrder();

		$parameters = mage::helper('gwap')->setOrder($order)->prepareData( $data->getGwapCcType() );

		$array = $data->getParameters();
		if (is_array($array)) {
			if (!in_array($parameters, $array)) {
				array_push($array, $parameters);
			}
		}
		else
		{
			$array = array($parameters);
		}
		$data->setParameters($array);

		//Salvar a condição de pagamento
		$condition = Mage::getSingleton('checkout/session')->getFvetsQuoteConditions();
		if ($condition)
			$data->setConditionData(Mage::getModel('fvets_payment/condition')->load($condition)->getData());

		//Salvar o id ERP do método de pagamento
		$data->setIdErp(Mage::getStoreConfig('allpago/gwap_cc/id_erp'));

		$gwap->setInfo(Mage::helper('core')->encrypt(serialize($data->toArray())));
		$gwap->save();
		$order->setInvoiceCapture(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);

		return $this;
	}

	public function validate() {
		return;
	}
}