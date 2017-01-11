<?php

class MageFM_MoIP_Model_CreditCard extends Mage_Payment_Model_Method_Cc
{

    protected $_code = 'magefm_moip_creditcard';
    protected $_formBlockType = 'magefm_moip/form_creditcard';

    public function assignData($data)
    {
        $parcelas = $data->getCcInstallments();

        if (empty($parcelas)) {
            $parcelas = '1';
        }

        $this->getInfoInstance()->setCcInstallments($parcelas);
        $this->getInfoInstance()->setPortadorNome($data->getPortadorNome());
        $this->getInfoInstance()->setPortadorNascimento($data->getPortadorNascimento());
        $this->getInfoInstance()->setPortadorCpf($data->getPortadorCpf());
        $this->getInfoInstance()->setPortadorTelefone($data->getPortadorTelefone());

        parent::assignData($data);
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        $token = $payment->getData('magefm_moip_token');

        if (empty($token)) {
            $token = Mage::helper('magefm_moip')->generateToken($payment->getOrder());

            $payment->setData('magefm_moip_token', $token);
            $payment->save();
        }

        $data = new stdClass();
        $data->Forma = 'CartaoCredito';
        $data->Instituicao = $this->convertCcType($payment->getData('cc_type'));
        $data->Parcelas = $payment->getData('cc_installments');

        if (empty($data->Parcelas)) {
            $data->Parcelas = '1';
        }

        $data->CartaoCredito = new stdClass();
        $data->CartaoCredito->Numero = $payment->getData('cc_number');
        $data->CartaoCredito->Expiracao = sprintf('%02d/%02d', $payment->getData('cc_exp_month'), substr($payment->getData('cc_exp_year'), 2, 2));
        $data->CartaoCredito->CodigoSeguranca = $payment->getData('cc_cid');

        $data->CartaoCredito->Portador = new stdClass();
        $data->CartaoCredito->Portador->Nome = $payment->getData('portador_nome');
        $data->CartaoCredito->Portador->DataNascimento = $payment->getData('portador_nascimento');
        $data->CartaoCredito->Portador->Telefone = $payment->getData('portador_telefone');
        $data->CartaoCredito->Portador->Identidade = $payment->getData('portador_cpf');

        $result = Mage::helper('magefm_moip')->pay($token, $data);
        $payment->setData('magefm_moip_result', json_encode($result));
        $payment->save();

        return $this;
    }

    protected function convertCcType($type)
    {
        switch ($type) {
            case 'VI':
                return 'Visa';
            case 'MC':
                return 'Mastercard';
            case 'AE':
                return 'AmericanExpress';
            case 'DN':
                return 'Diners';
            case 'HC':
                return 'Hipercard';
        }

        Mage::throwException('Invalid credit card type.');
    }

}
