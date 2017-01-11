<?php

	if(Mage::helper('core')->isModuleEnabled('Allpago_Gwap')){
		class FVets_Salesrep_Model_Order_Tmp extends Allpago_Gwap_Model_Sales_Order {}
	} else {
		class FVets_Salesrep_Model_Order_Tmp extends Mage_Sales_Model_Order {}
	}

	class FVets_Salesrep_Model_Order extends FVets_Salesrep_Model_Order_Tmp
	{

		const XML_PATH_EMAIL_COPY_TO_REP	= 'sales_email/order/copy_to_rep';

		protected function _getEmails($configPath)
		{
			$data = Mage::getStoreConfig($configPath, $this->getStoreId());

			if (Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_TO_REP, $this->getStoreId())) {
				$salesrep = Mage::getModel('customer/customer')->load($this->getCustomerId())->getFvetsSalesrep();
				$data .= ','.Mage::getModel('fvets_salesrep/salesrep')->load($salesrep)->getEmail();
			}

			if (!empty($data)) {
				if (!is_array($data))
				{
					$data = explode(',', $data);
				}

				$data = Mage::helper('fvets_core')->canSentEmailFromCustomer($this->getCustomerEmail(), $data);

				return $data;

			}
			return false;
		}

		public function queueNewOrderEmail($forceMode = false) {
			$orderComments = Mage::helper('fvets_sales')->getOrderCommentsBlockHtml($this);
			if($orderComments) {
				$this->setOrderCommentsBlock(nl2br($orderComments));
			}
			parent::queueNewOrderEmail($forceMode);
			try {
				//envia transacional para o representante com cópia do pedido
				Mage::helper('fvets_salesrep')->sendRepComissionEmail(null, $this);
			} catch (Exception $ex) {
				Mage::logException($e);
			}
			return $this;
		}
	}
?>