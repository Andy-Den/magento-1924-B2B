<?php

class FVets_Payment_Model_Gwap_Observer extends Allpago_Gwap_Model_Observer {

	/**
	 * Set forced canCreditmemo flag
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Mage_Payment_Model_Observer
	 */
	public function salesOrderSave($observer) {

		$orderId = $observer->getOrder()->getId();
		$order = Mage::getModel('sales/order')->load($orderId);

		$payment = $order->getPayment();

		if ($payment && is_object($payment) && !Mage::getStoreConfig('payment/gwap_cc/mc_active')
			|| !in_array($payment->getMethod(), array('gwap_cc', 'gwap_oneclick', 'gwap_boleto','gwap_2cc', 'gwap_deposito'))) {
			return $this;
		}

		$data = $this->getGwapPaymentData($payment);

		$mGwap = Mage::getModel('gwap/order');
		$mGwap->setStatus(Allpago_Gwap_Model_Order::STATUS_CREATED);
		$mGwap->setCreatedAt(Mage::getModel('core/date')->date("Y-m-d H:i:s"));
		// Não salvar dados do CC
//		if (!Mage::getStoreConfig('payment/gwap_cc/tipo_autorizacao') && $payment->getMethod() != "gwap_boleto") {
//			$mGwap->setInfo(Mage::helper('core')->encrypt(serialize($data->toArray())));
//		}
		$mGwap->setType(Mage::getStoreConfig('payment/' . $payment->getMethod() . '/mc_type'));
		$mGwap->setCcType($data->getCcType());
//		if($payment->getMethod() == 'gwap_2cc'){
//			$mGwap->setCcType2($data->getCcType2());
//		}
		$mGwap->setOrderId($order->getId());
		$mGwap->save();

//		if (Mage::getStoreConfig('payment/gwap_cc/tipo_autorizacao')) {
//			if ($payment->getMethod() != 'gwap_boleto') {
//				$result = $order->getPayment()->getMethodInstance()->authorizeNow($order,$data->toArray());
//				if($result && Mage::getStoreConfig('allpago/clearsale/active')
//					&& Mage::getStoreConfig('allpago/clearsale/produto') == 'clearid'
//					&& Mage::getStoreConfig('allpago/clearsale/clearid_questionario')){
//					Mage::getModel('clearsale/clearid')->SubmitInfo($order);
//				}
//			}
//		}

		if ($payment->getMethod() == "gwap_boleto") {
			$mGwap->setInfo(Mage::helper('core')->encrypt(serialize($data->toArray())));
			$mGwap->save();
			$log = Mage::getModel('allpago_mc/log');
			try{
				//Fazer regras de condiçoes de pagamento
				if ($condition = Mage::getSingleton('checkout/session')->getFvetsQuoteConditions())
				{
					//Pega a condiçao selecionada
					$condition = Mage::getModel('fvets_payment/condition')->load($condition);

					//

					//Divide as partelas em partes iguais
					$splitGrandTotal = $order->getGrandTotal() / $condition->getSplit();
					$condition->setSplitGrandTotal($splitGrandTotal);

					//Create a condition session
					$paymentSession = Mage::getSingleton('fvets_payment/session');


					//Cria os varios boletos
					for($i = 0; $i < $condition->getSplit(); $i++)
					{
						$condition->setExpireDays($condition->getStartDays() + ($condition->getSplitRange() * $i));

						$paymentSession->setCondition($condition);

						$order->getPayment()->getMethodInstance()->validateAutorize($order->getPayment(), $condition->getSplitGrandTotal());
					}
				}
				else
				{
					$order->getPayment()->getMethodInstance()->validateAutorize($order->getPayment(), $order->getGrandTotal());
				}

				$gwapNovo = Mage::getModel('gwap/order')->load( $order->getId(), 'order_id');
				$gwapNovo->setStatus(Allpago_Gwap_Model_Order::STATUS_CAPTUREPAYMENT);
				$gwapNovo->setErrorCode(null);
				$gwapNovo->setErrorMessage(null);
				$gwapNovo->setTries(0);
				$gwapNovo->setAbandoned(0);
				$gwapNovo->setUpdatedAt(Mage::getModel('core/date')->date("Y-m-d H:i:s"));
				$gwapNovo->save();
				$log->add($gwapNovo->getOrderId(), 'Payment', 'authorize()', Allpago_Gwap_Model_Order::STATUS_AUTHORIZED, 'Boleto gerado');
			}catch (Exception $e) {
				//Salva log
				$log->add($mGwap->getOrderId(), '+ Conversao', 'authorize()', Allpago_Gwap_Model_Order::STATUS_ERROR, 'Ocorreu um erro', $e->getMessage());
				$mGwap->setUpdatedAt(Mage::getModel('core/date')->date("Y-m-d H:i:s"));
				$mGwap->save();
				//$url = Mage::getUrl('sales/order/view', array('order_id'=>$order->getId()));
				//$linkMessage = Mage::helper('gwap')->__('Clique aqui');
				//$this->getResponse()->setBody( sprintf( Mage::helper('gwap')->__('Não foi possível gerar seu boleto no momento. Você pode reimprimir acessando o detalhe de seu pedido. %s.'), '<a href="'.$url.'" target="_blank" class="imprimir_boleto">'.$linkMessage.'</a>' ) );
				return $this;
			}
		}

		if ($payment->getMethod() == "gwap_cc") {
			$mGwap->setInfo(Mage::helper('core')->encrypt(serialize($data->toArray())));
			$mGwap->save();
			$log = Mage::getModel('allpago_mc/log');
			try {
				//Fazer regras de condiçoes de pagamento
				if ($condition = Mage::getSingleton('checkout/session')->getFvetsQuoteConditions()) {
					//Pega a condiçao selecionada
					$condition = Mage::getModel('fvets_payment/condition')->load($condition);

					//

					//Divide as partelas em partes iguais
					$splitGrandTotal = $order->getGrandTotal() / $condition->getSplit();
					$condition->setSplitGrandTotal($splitGrandTotal);

					//Create a condition session
					$paymentSession = Mage::getSingleton('fvets_payment/session');


					//Cria os varios boletos
					for ($i = 0; $i < $condition->getSplit(); $i++) {
						$condition->setExpireDays($condition->getStartDays() + ($condition->getSplitRange() * $i));

						$paymentSession->setCondition($condition);

						$order->getPayment()->getMethodInstance()->validateAutorize($order->getPayment(), $condition->getSplitGrandTotal());
					}
				} else {
					$order->getPayment()->getMethodInstance()->validateAutorize($order->getPayment(), $order->getGrandTotal());
				}

				$gwapNovo = Mage::getModel('gwap/order')->load($order->getId(), 'order_id');
				$gwapNovo->setStatus(Allpago_Gwap_Model_Order::STATUS_CAPTUREPAYMENT);
				$gwapNovo->setErrorCode(null);
				$gwapNovo->setErrorMessage(null);
				$gwapNovo->setTries(0);
				$gwapNovo->setAbandoned(0);
				$gwapNovo->setUpdatedAt(Mage::getModel('core/date')->date("Y-m-d H:i:s"));
				$gwapNovo->save();
				$log->add($gwapNovo->getOrderId(), 'Payment', 'authorize()', Allpago_Gwap_Model_Order::STATUS_AUTHORIZED, 'Descrição do pagamento por cartão gerado');
			} catch (Exception $e) {
				//Salva log
				$log->add($mGwap->getOrderId(), '+ Conversao', 'authorize()', Allpago_Gwap_Model_Order::STATUS_ERROR, 'Ocorreu um erro', $e->getMessage());
				$mGwap->setUpdatedAt(Mage::getModel('core/date')->date("Y-m-d H:i:s"));
				$mGwap->save();
				//$url = Mage::getUrl('sales/order/view', array('order_id'=>$order->getId()));
				//$linkMessage = Mage::helper('gwap')->__('Clique aqui');
				//$this->getResponse()->setBody( sprintf( Mage::helper('gwap')->__('Não foi possível gerar seu boleto no momento. Você pode reimprimir acessando o detalhe de seu pedido. %s.'), '<a href="'.$url.'" target="_blank" class="imprimir_boleto">'.$linkMessage.'</a>' ) );
				return $this;
			}
		}

			if ($payment->getMethod() == "gwap_deposito") {
				$mGwap->setInfo(Mage::helper('core')->encrypt(serialize($data->toArray())));
				$mGwap->save();
				$log = Mage::getModel('allpago_mc/log');
				try{
					//Fazer regras de condiçoes de pagamento
					if ($condition = Mage::getSingleton('checkout/session')->getFvetsQuoteConditions())
					{
						//Pega a condiçao selecionada
						$condition = Mage::getModel('fvets_payment/condition')->load($condition);

						//

						//Divide as partelas em partes iguais
						$splitGrandTotal = $order->getGrandTotal() / $condition->getSplit();
						$condition->setSplitGrandTotal($splitGrandTotal);

						//Create a condition session
						$paymentSession = Mage::getSingleton('fvets_payment/session');


						//Cria os varios boletos
						for($i = 0; $i < $condition->getSplit(); $i++)
						{
							$condition->setExpireDays($condition->getStartDays() + ($condition->getSplitRange() * $i));

							$paymentSession->setCondition($condition);

							$order->getPayment()->getMethodInstance()->validateAutorize($order->getPayment(), $condition->getSplitGrandTotal());
						}
					}
					else
					{
						$order->getPayment()->getMethodInstance()->validateAutorize($order->getPayment(), $order->getGrandTotal());
					}

					$gwapNovo = Mage::getModel('gwap/order')->load( $order->getId(), 'order_id');
					$gwapNovo->setStatus(Allpago_Gwap_Model_Order::STATUS_CAPTUREPAYMENT);
					$gwapNovo->setErrorCode(null);
					$gwapNovo->setErrorMessage(null);
					$gwapNovo->setTries(0);
					$gwapNovo->setAbandoned(0);
					$gwapNovo->setUpdatedAt(Mage::getModel('core/date')->date("Y-m-d H:i:s"));
					$gwapNovo->save();
					$log->add($gwapNovo->getOrderId(), 'Payment', 'authorize()', Allpago_Gwap_Model_Order::STATUS_AUTHORIZED, 'Descrição do pagamento por depósito gerado');
				}catch (Exception $e) {
					//Salva log
					$log->add($mGwap->getOrderId(), '+ Conversao', 'authorize()', Allpago_Gwap_Model_Order::STATUS_ERROR, 'Ocorreu um erro', $e->getMessage());
					$mGwap->setUpdatedAt(Mage::getModel('core/date')->date("Y-m-d H:i:s"));
					$mGwap->save();
					//$url = Mage::getUrl('sales/order/view', array('order_id'=>$order->getId()));
					//$linkMessage = Mage::helper('gwap')->__('Clique aqui');
					//$this->getResponse()->setBody( sprintf( Mage::helper('gwap')->__('Não foi possível gerar seu boleto no momento. Você pode reimprimir acessando o detalhe de seu pedido. %s.'), '<a href="'.$url.'" target="_blank" class="imprimir_boleto">'.$linkMessage.'</a>' ) );
					return $this;
				}
			}
		return $this;
	}

	public function addBoletoLink($observer) {
		return $this;
	}

}