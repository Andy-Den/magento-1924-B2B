<?php
class FVets_Payment_Block_Gwap_Info_Cc extends Allpago_Gwap_Block_Info_Cc {

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
		if ($this->getInfo()->getOrder() && $this->getInfo()->getOrder()->hasData()) {
			$order = $this->getInfo()->getOrder();
			if ($this->getInfo()->getOrder()->getId()) {
				$gwapItem = Mage::getModel('gwap/order')->load($this->getInfo()->getOrder()->getId(), 'order_id');

				$dataItem = new Varien_Object(unserialize(Mage::helper('core')->decrypt($gwapItem->getInfo())));

				$conditionData = $dataItem->getConditionData();
				//$transport->addData($conditionData);
				if ($conditionData) {
					$transport->addData(array(
						'Condição de pagamento' => $conditionData['name']
					));
				}
			}
		}

		return $transport->setData(array_merge($data, $transport->getData()));
	}
}