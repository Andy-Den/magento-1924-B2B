<?php

class MageFM_MoIP_Model_Boleto extends Mage_Payment_Model_Method_Abstract
{

    protected $_code = 'magefm_moip_boleto';

    public function assignData($data)
    {
        $parcelas = $data->getCcInstallments();

        if (empty($parcelas)) {
            $parcelas = '1';
        }

        $this->getInfoInstance()->setCcInstallments($parcelas);

        parent::assignData($data);
    }

    public function order(Varien_Object $payment, $amount)
    {
        $token = $payment->getData('magefm_moip_token');
        $comissao = (float) $this->getConfigData('comissao');

        if (empty($token)) {
            $token = Mage::helper('magefm_moip')->generateToken($payment->getOrder(), $comissao);

            $payment->setData('magefm_moip_token', $token);
            $payment->save();
        }

        $data = new stdClass();
        $data->Forma = 'BoletoBancario';

        $result = Mage::helper('magefm_moip')->pay($token, $data);
        $payment->setData('magefm_moip_result', json_encode($result));
        $payment->save();

        return $this;
    }

}
