<?php

class MageFM_MoIP_Model_Api
{

    protected $environment;
    protected $token;
    protected $key;

    public function generateToken(Mage_Sales_Model_Order $order)
    {
        try {
            $requestXML = $this->generateAuthXML($order);
            Mage::log($requestXML, null, 'moip-request-xml.log', true);
            $responseXML = $this->sendXML($requestXML);
            Mage::log($responseXML, null, 'moip-response-xml.log', true);
            $response = simplexml_load_string($responseXML);

            if (empty($response)) {
                Mage::throwException('Invalid response.');
            }

            if ((string) $response->Resposta->Status === 'Sucesso') {
                return (string) $response->Resposta->Token;
            }

            Mage::throwException((string) $response->Resposta->Erro);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    public function preCadastro($requestXML)
    {
        try {
            $responseXML = $this->sendXMLPreCad($requestXML);
            $response = simplexml_load_string($responseXML);

            if (empty($response)) {
                throw new Exception('Invalid response.');
            }

            return $response;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function cadastro($vendor, $requestXML)
    {
        $response = $this->preCadastro($requestXML);
        if($response->RespostaPreCadastramento->Status == 'Sucesso') {
            $cobrand = Mage::getStoreConfig('portal_configuration/cobrand');
            $redirect_url = $cobrand['redirect_url'].'/'.$cobrand['cobrand_key'].'/'.$response->RespostaPreCadastramento->idRedirecionamento;
            $vendor->setData('moip_customer_id', $response->RespostaPreCadastramento->idRedirecionamento);
            $vendor->save();
            Mage::app()->getFrontController()->getResponse()->setRedirect($redirect_url);
        } elseif($response->RespostaPreCadastramento->Erro == 'E-mail já cadastrado') {
            return array('tipo' => 'info', 'msg' => 'Já cadastrado no MoIP com o email: '.$response->RespostaPreCadastramento->Login, 'email' => $response->RespostaPreCadastramento->Login);
        } else {
            return  array('tipo' => 'erro', 'msg' => 'Ocorreu um erro ao fazer o cadastro'.$response->RespostaPreCadastramento->Erro);
        }

    }

    public function pay($token, $data)
    {
        try {
            $payment = array(
                'pagamentoWidget' => array(
                    'referer' => Mage::getUrl(),
                    'token' => $token,
                    'dadosPagamento' => $data
                )
            );

            $client = new Zend_Http_Client($this->environment . '/rest/pagamento?callback=?');
            $client->setMethod(Zend_Http_Client::GET);
            $client->setHeaders('Accept-encoding', 'identity');
            $client->setHeaders('Content-Type', 'application/json');
            $client->setHeaders('Accept', 'application/json');
            $client->setParameterGet('pagamentoWidget', json_encode($payment));
            Mage::log(json_encode($payment), null, 'moip-request-json.log', true);
            $responseObject = $client->request();

            Mage::log($responseObject->getBody(), null, 'moip-response-json.log', true);
            $response = json_decode(substr($responseObject->getBody(), 2, -1));

            if ((string) $response->StatusPagamento === 'Sucesso') {
                return $response;
            }

            Mage::throwException((string) $response->Mensagem);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    protected function generateAuthXML($order)
    {
        $root = simplexml_load_string('<EnviarInstrucao />');

        $instrucaoUnica = $root->addChild('InstrucaoUnica');
        $instrucaoUnica->addAttribute('TipoValidacao', 'Transparente');

        $instrucaoUnica->addChild('Razao', 'Compra e-commerce');

        $valores = $instrucaoUnica->addChild('Valores');
        $valor = $valores->addChild('Valor', number_format($this->calculateOrderTotalWithInterest($order), 2, '.', ''));
        $valor->addAttribute('Moeda', 'BRL');

        $instrucaoUnica->addChild('IdProprio', $order->getIncrementId());

        $pagador = $instrucaoUnica->addChild('Pagador');
        $pagador->addChild('IdPagador', $order->getCustomerId());
        $pagador->addChild('Nome', $order->getCustomer()->getName());
        $pagador->addChild('Email', $order->getCustomerEmail());

        $address = $order->getBillingAddress();
        $telephone = preg_replace('/([^0-9()-])/', '', $address->getTelephone());

        if ($address->getRegionId()) {
            $region = Mage::getModel('directory/region')->load($address->getRegionId());

            if ($region->getId()) {
                $region = $region->getCode();
            }
        }

        if (!isset($region)) {
            $region = $address->getRegion();
        }

        $enderecoCobranca = $pagador->addChild('EnderecoCobranca');
        $enderecoCobranca->addChild('Logradouro', $address->getStreet(1));
        $enderecoCobranca->addChild('Numero', $address->getStreet(2));
        $enderecoCobranca->addChild('Complemento', $address->getStreet(3));
        $enderecoCobranca->addChild('Bairro', $address->getBairro());
        $enderecoCobranca->addChild('Cidade', $address->getCity());
        $enderecoCobranca->addChild('Estado', $region);
        $enderecoCobranca->addChild('Pais', 'BRA');
        $enderecoCobranca->addChild('CEP', $address->getPostcode());
        $enderecoCobranca->addChild('TelefoneFixo', $telephone);

        $comissoes = $this->calculateComissoes($order);

        if (count($comissoes) > 0) {
            $nodeComissoes = $instrucaoUnica->addChild('Comissoes');

            foreach ($comissoes as $loginMoIP => $valorComissao) {
                $comissionamento = $nodeComissoes->addChild('Comissionamento');
                $comissionamento->addChild('Razao', 'Compra na 4Vets #' . $order->getIncrementId());
                $comissionamento->addChild('ValorFixo', number_format($valorComissao, 2, '.', ''));
                $comissionado = $comissionamento->addChild('Comissionado');
                $comissionado->addChild('LoginMoIP', $loginMoIP);
            }
        }

        return $root->asXML();
    }

    protected function sendXML($xml)
    {
        $client = new Zend_Http_Client($this->environment . '/ws/alpha/EnviarInstrucao/Unica');
        $client->setMethod(Zend_Http_Client::POST);
        $client->setAuth($this->token, $this->key);
        $client->setHeaders('Accept-encoding', 'identity');
        $client->setRawData($xml);

        $result = $client->request();
        return $result->getBody();
    }

    protected function sendXMLPreCad($xml)
    {
        $client = new Zend_Http_Client(Mage::getStoreConfig('portal_configuration/cobrand/url'));
        $client->setMethod(Zend_Http_Client::POST);
        $client->setAuth($this->token, $this->key);
        $client->setHeaders('Accept-encoding', 'identity');
        $client->setRawData($xml);

        $result = $client->request();
        return $result->getBody();
    }

    protected function calculateComissoes($order)
    {
        $comissoes = array();
        /** @TODO usar config */
        $defaultComissao = 5.00;
        /** @TODO usar config */
        $taxaMoIPVendor = 3.19;

        $vComissao = array();
        $vItems = array();
        $vProporcao = array();

        foreach ($order->getAllItems() as $item) {
            $comissao = $defaultComissao;
            $vendor = Mage::helper('udropship')->getVendor($item->getData('udropship_vendor'));

            //pega o valor da comissao padrao do vendor
            if (!is_null($vendor->getData('comission'))) {
                $comissao = (float) $vendor->getData('comission');
            }

            if (!isset($comissoes[$vendor->getData('login_moip')])) {
                $comissoes[$vendor->getData('login_moip')] = 0;
            }

            $comissao += $taxaMoIPVendor;
            $comissionado = $vendor->getData('login_moip');
            $vComissao[$comissionado] = $comissao;
            $vItems[$comissionado] = $item->getRowTotal();
            $vProporcao[$comissionado] =  $item->getRowTotal()/$order->getSubtotal();
            $comissoes[$comissionado] += $this->calculateValorItemSemComissao((float) ($item->getRowTotal() - $item->getDiscountAmount()), $comissao);
        }

        $shippingDetails = json_decode($order->getData('udropship_shipping_details'));

        foreach ($shippingDetails->methods as $vendorId => $details) {
            $vendor = Mage::helper('udropship')->getVendor($vendorId);

            if (!isset($comissoes[$vendor->getData('login_moip')])) {
                $comissoes[$vendor->getData('login_moip')] = 0;
            }

            $valor = ((float) $details->price) * (100 - $taxaMoIPVendor) / 100;
            $comissoes[$vendor->getData('login_moip')] += $valor;
        }

        return $comissoes;
    }

    protected function calculateValorItemSemComissao($total, $comissao)
    {
        return ($total * (100 - $comissao)) / 100;
    }

    protected function calculateOrderTotalWithInterest($order)
    {
        $total = $order->getGrandTotal();
        $numeroParcelas = (int) $order->getPayment()->getData('cc_installments');

        return Mage::helper('magefm_moip')->calculateOrderTotalWithInterest($total, $numeroParcelas);
    }

    public function checkLogin($login)
    {

        if(strlen($login) == 0) {
            return false;
        }

        try {
            $url = $this->environment . '/ws/alpha/VerificarConta/'.$login;
            $client = new Zend_Http_Client($url);
            $client->setMethod(Zend_Http_Client::GET);
            $client->setAuth($this->token, $this->key);
            $client->setHeaders('Accept-encoding', 'identity');
            $client->setHeaders('Content-Type', 'application/json');
            $client->setHeaders('Accept', 'application/json');

            $responseObject = $client->request();
            $response = simplexml_load_string($responseObject->getBody());

            if($response->RespostaVerificarConta->Status->__toString() == 'Criado') {
                return true;
            }

            return false;
        } catch(Exception $e) {
            return false;
        }

        return false;
    }

}
