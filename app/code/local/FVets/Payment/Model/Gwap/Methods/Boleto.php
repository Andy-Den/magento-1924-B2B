<?php

class FVets_Payment_Model_Gwap_Methods_Boleto extends  Allpago_Gwap_Model_Methods_Boleto
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

		$parameters = mage::helper('gwap')->setOrder($order)->prepareData( $data->getGwapBoletoType() );

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

		$data->setGenerateBoleto(Mage::getStoreConfig('allpago/gwap_boleto/generate_boleto'));

		//Salvar a condição de pagamento
		$condition = Mage::getSingleton('checkout/session')->getFvetsQuoteConditions();
		if ($condition)
			$data->setConditionData(Mage::getModel('fvets_payment/condition')->load($condition)->getData());

		//Salvar o id ERP do método de pagamento
		$data->setIdErp(Mage::getStoreConfig('allpago/gwap_boleto/id_erp'));

		$gwap->setInfo(Mage::helper('core')->encrypt(serialize($data->toArray())));
		$gwap->save();

		//Se for permitido gerar o boleto bancario
		if (Mage::getStoreConfig('allpago/gwap_boleto/generate_boleto'))
		{
			//Se gerar o boleto, capturar online
			$order->setInvoiceCapture(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);

			return $this->authorize($payment, $amount);
		}
		else
		{
			//Se não gerado boleto, capturar offline
			$order->setInvoiceCapture(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
			/*$log = Mage::getModel('allpago_mc/log');
			$log->add($order->getId(), 'Payment', 'authorize()',  Allpago_Gwap_Model_Order::STATUS_FINISHED, 'O boleto deve ser gerado pela distribuidora.', $amount);*/
		}

		return $this;
	}

	/**
	 * Authorize
	 *
	 * @param   Varien_Object $orderPayment
	 * @param float $amount
	 * @return  Mage_Payment_Model_Abstract
	 */
	public function authorize(Varien_Object $payment, $amount) {
		$config = mage::helper('gwap')->getConfig();
		$auth = mage::helper('gwap')->getAuthConfig();
		$gwap = Mage::getModel('gwap/order')->load($payment->getOrder()->getId(), 'order_id');
		$data = new Varien_Object(unserialize(Mage::helper('core')->decrypt($gwap->getInfo())));
		$order = $payment->getOrder();

		$url = '';
		if ($auth->getAmbiente() == 'LIVE') {
			$url = "https://ctpe.net/frontend/payment.prc";
		} elseif ($auth->getAmbiente() == 'CONNECTOR_TEST') {
			$url = "https://test.ctpe.net/frontend/payment.prc";
		}

		$parameters = mage::helper('gwap')->setOrder($order)->prepareData( $data->getGwapBoletoType() );

		//Mage::log(print_r($parameters,true),null,'boleto_allpago.log');
		//prepare params
		foreach (array_keys($parameters) AS $key) {
			if (!isset($$key)) {
				$$key = '';
			}

			if (!isset($result)) {
				$result = '';
			}

			$$key .= $parameters[$key];
			$$key = urlencode($$key);
			$$key .= "&";

			$var = $key;

			$value = $$key;
			$result .= "$var=$value";
		}

		$strPOST = stripslashes($result);

		// open the request url for the Web Payment Frontend
		$cpt = curl_init();
		curl_setopt($cpt, CURLOPT_URL, $url);
		curl_setopt($cpt, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($cpt, CURLOPT_USERAGENT, "php ctpepost");
		curl_setopt($cpt, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cpt, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($cpt, CURLOPT_POST, 1);
		curl_setopt($cpt, CURLOPT_POSTFIELDS, $strPOST);
		//curl_setopt($cpt, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8"));

		$curlresultURL = curl_exec($cpt);
		$curlerror = curl_error($cpt);
		$curlinfo = curl_getinfo($cpt);

		curl_close($cpt);

		$r_arr = explode("&", $curlresultURL);

		foreach ($r_arr AS $buf) {
			$temp = urldecode($buf);
			$temp = explode("=", $temp, 2);
			$postatt = $temp[0];
			$postvar = $temp[1];
			$returnvalue[$postatt] = $postvar;
		}

		//Zend_debug::dump($parameters);
		if( !isset($returnvalue['PROCESSING.CODE']) ){
			try{
				Mage::throwException(Mage::helper('gwap')->__('Falha ao gerar boleto'));
			} catch (Exception $e) {
				$this->redirect($order,$e->getMessage());
			}
		}
		$resultCode = explode('.', $returnvalue['PROCESSING.CODE']);

		// validate Pre authorization - 90 success code
		if ($resultCode[2] != '90') {
			try{
				Mage::throwException(Mage::helper('gwap')->__($returnvalue['PROCESSING.REASON'] . ' - ' . $returnvalue['PROCESSING.RETURN']));
			} catch (Exception $e) {
				$this->redirect($order,$e->getMessage());
			}
		}

		if( $returnvalue['PROCESSING.CONNECTORDETAIL.EXTERNAL_SYSTEM_LINK'] ){
			$redirect_url = $returnvalue['PROCESSING.CONNECTORDETAIL.EXTERNAL_SYSTEM_LINK'];
		}else{
			$redirect_url = $returnvalue['PROCESSING.REDIRECT.URL'] . '?DC=' . $returnvalue['PROCESSING.REDIRECT.PARAMETER.DC'];
		}

		$boletoLink = $redirect_url;

		//Corrigir bug no link do Bradesco
		if(Mage::getStoreConfig('payment/gwap_boleto/types') == 'BRADESCO'){
			$boletoLink =  str_replace('sepsBoletoRet/004591070', 'paymethods/boletoret/model1', $redirect_url );
		}

		$array = $data->getBoletoLink();
		if (is_array($array)) {
			array_push($array, $boletoLink);
		}
		else
		{
			$array = array($boletoLink);
		}

		$data->setBoletoLink($array);

		$gwap->setInfo(Mage::helper('core')->encrypt(serialize($data->toArray())) );

		$gwap->save();

		return $this;
	}

	/**
	 * Capture
	 *
	 * @param   Varien_Object $orderPayment
	 * @param float $amount
	 * @return  Mage_Payment_Model_Abstract
	 */
	public function capture(Varien_Object $payment, $amount) {

		$order = $payment->getOrder();

		if ($order->getInvoiceCapture() == Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE)
		{
			return parent::capture($payment, $amount);
		}
		else
		{

		}

	}
}