<?php
class FVets_Payment_Block_Gwap_Info_Boleto extends Allpago_Gwap_Block_Info_Boleto {

	/**
	 * Prepare credit card related payment info
	 *
	 * @param Varien_Object|array $transport
	 * @return Varien_Object
	 */
	protected function _prepareSpecificInformation($transport = null)
	{
		if (null !== $this->_paymentSpecificInformation) {
			return $this->_paymentSpecificInformation;
		}
		//$transport = parent::_prepareSpecificInformation($transport);
		$transport = call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareSpecificInformation'), $transport);
		$data = array();
		if ($this->getInfo()->getGwapBoletoType()) {
			$data[Mage::helper('payment')->__('Banco')] = $this->getInfo()->getGwapBoletoType();
		}
		if( $this->getInfo()->getOrder() && $this->getInfo()->getOrder()->hasData() ){
			$order = $this->getInfo()->getOrder();
			$orderId = $order->getId();
			$customerId = $order->getCustomerId();
			if ($this->getInfo()->getOrder()->getId()) {
				$gwapItem = Mage::getModel('gwap/order')->load($this->getInfo()->getOrder()->getId(), 'order_id');
				if( $gwapItem->getStatus() == Allpago_Gwap_Model_Order::STATUS_CAPTUREPAYMENT || $gwapItem->getStatus() == Allpago_Gwap_Model_Order::STATUS_CREATED ){
					$dataItem = new Varien_Object(unserialize(Mage::helper('core')->decrypt($gwapItem->getInfo())));

					$conditionData = $dataItem->getConditionData();
					//$transport->addData($conditionData);
					if ($conditionData) {
						$transport->addData(array(
							'Condição de pagamento' => $conditionData['name']
						));
					}

					$store = Mage::getModel('core/store')->load($order->getStoreId());
					/* @var $store Mage_Core_Model_Store */
					//$boletoUrl = $store->getUrl('allpago_gwap/imprimir/boleto', array('id' => $orderId, 'ci' => $customerId));
					if ($dataItem->getGenerateBoleto()) {
						$boletoLink = $dataItem->getBoletoLink();
						$parameters = $dataItem->getParameters();
						$links = array();
						for ($i = 0; $i < count($boletoLink); $i++)
						{
							if (isset($parameters[$i]['CRITERION.BRADESCO_datavencimento'])) {
								$vencimento = $parameters[$i]['CRITERION.BRADESCO_datavencimento'];
							} else if (isset($parameters[$i]['CRITERION.BOLETO_Due_date'])) {
								$vencimento = substr($parameters[$i]['CRITERION.BOLETO_Due_date'], 0,2) . '/' . substr($parameters[$i]['CRITERION.BOLETO_Due_date'], 2,2) . '/' . substr($parameters[$i]['CRITERION.BOLETO_Due_date'], 4);
							}
							$links['Boleto' . ($i + 1)] = '<a href="'.$boletoLink[$i].'" target="_blank">Vencimento: '.$vencimento.' | Valor: '.Mage::helper('core')->currency($parameters[$i]['PRESENTATION.AMOUNT']).'</a>';
						}
						$transport->addData($links);
					} else {
						$parameters = $dataItem->getParameters();
						$links = array();
						for ($i = 0; $i < count($parameters); $i++)
						{
							if (isset($parameters[$i]['CRITERION.BRADESCO_datavencimento'])) {
								$vencimento = $parameters[$i]['CRITERION.BRADESCO_datavencimento'];
							} else if (isset($parameters[$i]['CRITERION.BOLETO_Due_date'])) {
								$vencimento = substr($parameters[$i]['CRITERION.BOLETO_Due_date'], 0,2) . '/' . substr($parameters[$i]['CRITERION.BOLETO_Due_date'], 2,2) . '/' . substr($parameters[$i]['CRITERION.BOLETO_Due_date'], 4);
							}
							$links['Boleto' . ($i + 1)] = 'Vencimento: '.$vencimento.' | Valor: '.Mage::helper('core')->currency($parameters[$i]['PRESENTATION.AMOUNT']).' | O boleto deve ser gerado pela distribuidora.';
						}
						$transport->addData($links);
					}


				}
			}
		}

		return $transport->setData(array_merge($data, $transport->getData()));
	}

}