<?php


class FVets_Payment_Helper_Gwap extends Allpago_Gwap_Helper_Data
{
	protected $_config = null;

	/*public function prepareData( $type )
	{
		if (!$this->_config)
			$this->_config = $this->getConfig();
		//Configurar a data de vencimento de acordo com o split
		$this->condition = Mage::getSingleton('fvets_payment/session')->getCondition();
		if ($this->condition->getExpireDate())
			$this->setConfig('vencimento', $this->condition->getExpireDate());

		return parent::prepareData($type);
	}*/

	public function prepareData( $type ){

		$condition = Mage::getSingleton('fvets_payment/session')->getCondition();

		$order = $this->getOrder();
		$config = $this->getConfig();
		$auth = $this->getAuthConfig();

		$parameters = array();

		//prepare parameters
		$parameters['RESPONSE.VERSION'] = '1.0';
		$parameters['TRANSACTION.MODE'] = $auth->getAmbiente(); #####PEGAR AMBIENTE######
		$parameters['TRANSACTION.RESPONSE'] = 'SYNC';
		$parameters['SECURITY.SENDER'] = trim($auth->getSecuritySender());

		$transaction_type = 'transaction_channel_'.strtolower($type);

		$parameters['TRANSACTION.CHANNEL'] = trim($config->getData($transaction_type)); #####PEGAR CANAL######
		$parameters['USER.LOGIN'] = trim($auth->getUserLogin());
		$parameters['USER.PWD'] = strval(Mage::helper("core")->decrypt($auth->getUserPwd()));

		$parameters['IDENTIFICATION.TRANSACTIONID'] = $order->getIncrementId();

		$parameters['PAYMENT.CODE'] = 'PP.PA';
		if (is_object($condition) && $condition->getSplitGrandTotal())
			$parameters['PRESENTATION.AMOUNT'] = number_format($condition->getSplitGrandTotal(), 2, '.', '');
		else
			$parameters['PRESENTATION.AMOUNT'] = number_format($order->getGrandTotal(), 2, '.', '');

		$parameters['PRESENTATION.CURRENCY'] = "BRL";

		$street = utf8_decode($order->getBillingAddress()->getStreet(1));
		if (strlen($street) < 5) {
			$street = 'Rua ' . utf8_decode($order->getBillingAddress()->getStreet(1));
		}

		$parameters['ADDRESS.STREET'] = $street;
		$parameters['ADDRESS.ZIP'] = str_replace('-', '', utf8_decode($order->getBillingAddress()->getPostcode()));

		$city = $order->getBillingAddress()->getCity();

		$parameters['ADDRESS.CITY'] = utf8_decode($city);
		$parameters['ADDRESS.COUNTRY'] = utf8_decode($order->getBillingAddress()->getCountryId());
		$parameters['ADDRESS.STATE'] = $order->getBillingAddress()->getRegionId()
			? Mage::getModel('directory/region')->load( $order->getBillingAddress()->getRegionId() )->getCode()
			:  $order->getBillingAddress()->getRegion();

		$parameters['CONTACT.EMAIL'] = trim($order->getBillingAddress()->getEmail())
			? trim(utf8_decode($order->getBillingAddress()->getEmail())) : trim(utf8_decode($order->getCustomerEmail()));

		$parameters['NAME.GIVEN'] = utf8_decode($order->getBillingAddress()->getFirstname());
		$parameters['NAME.FAMILY'] = utf8_decode($order->getBillingAddress()->getLastname());

		if (is_object($condition) && $condition->getExpireDays())
			$vencimento = $condition->getExpireDays();
		else
			$vencimento = $config->getVencimento();

		if( is_numeric($vencimento) && $vencimento > 0 ){
			$due_date = Mage::getModel('core/date')->timestamp( '+'.$vencimento.' days' );
		}else{
			$due_date = Mage::getModel('core/date')->timestamp( '+1 day' );
		}

		$cpf = $order->getCustomerTaxvat();
		if(!$cpf){
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
			$customerDocs = explode(",", Mage::getStoreConfig('allpago/gwap_boleto/campo_documento'));
			$cpf = null;

			foreach ($customerDocs as $customerDoc) {
				$metodo = 'get' . ucfirst($customerDoc);
				if (!$cpf && $customer->$metodo()) {
					$cpf = (string) preg_replace('/[^0-9]/', '', $customer->$metodo());
				}
			}
		}

		switch ($type){

			case null:
				if( $config->getInstrucoes() ){

					$instrucoes = explode( PHP_EOL, $config->getInstrucoes() );
					foreach ( $instrucoes as $key => $inst){
						$parameters['CRITERION.BRADESCO_instrucao'.($key+1)]  = $inst;
					}
				}

				$parameters['CRITERION.BRADESCO_numeropedido']  = $order->getIncrementId();
				$parameters['CRITERION.BRADESCO_datavencimento']  = date( 'd/m/Y', $due_date );
				$parameters['CRITERION.BRADESCO_cpfsacado']  = (string) str_replace(array('.','-',' '), array('', '', ''), $cpf);

				break;

			case 'BRADESCO':

				/*if( is_numeric($vencimento) && $vencimento > 3 ){
					$due_date = Mage::getModel('core/date')->timestamp( '+3 days' );
				}*/
				if( $config->getInstrucoes() ){

					$instrucoes = explode( PHP_EOL, $config->getInstrucoes() );
					foreach ( $instrucoes as $key => $inst){
						$parameters['CRITERION.BRADESCO_instrucao'.($key+1)]  = $inst;
					}
				}

				$parameters['CRITERION.BRADESCO_numeropedido']  = $order->getIncrementId();
				$parameters['CRITERION.BRADESCO_datavencimento']  = date( 'd/m/Y', $due_date );
				$parameters['CRITERION.BRADESCO_cpfsacado']  = (string) str_replace(array('.','-',' '), array('', '', ''), $cpf);

				break;

			case 'ITAU':

				$parameters['CRITERION.BOLETO_Due_date']  =  date( 'dmY', $due_date );
				$parameters['CRITERION.BOLETO_Codeenrollment']  = '01';
				$parameters['CRITERION.BOLETO_Numberenrollment']  = (string) str_pad( str_replace(array('.','-',' '), array('', '', ''), $cpf), 14, "0", STR_PAD_LEFT);
				$parameters['CRITERION.BOLETO_BairroSacado']  = $order->getBillingAddress()->getStreet(4);

				break;
		}

		return $parameters;
	}
}