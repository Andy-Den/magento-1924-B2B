<?php

class MageFM_Moip_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $model;

    public function generateToken($order)
    {
        return $this->getModel()->generateToken($order);
    }

    public function pay($token, $data)
    {
        return $this->getModel()->pay($token, $data);
    }

    public function getBoletoUrl($order)
    {
        return $this->getEnvironment() . '/Instrucao.do?token=' . $order->getPayment()->getData('magefm_moip_token');
    }

    public function cadastro($vendor, $requestXML)
    {
        return $this->getModel()->cadastro($vendor, $requestXML);
    }

    public function checkLogin($login)
    {
        return $this->getModel()->checkLogin($login);
    }

    protected function getModel()
    {
        if (is_null($this->model)) {
            $this->model = Mage::getSingleton('magefm_moip/api');
            $this->model->setEnvironment($this->getEnvironment());
            $this->model->setToken(Mage::getStoreConfig('payment/magefm_moip/access_token'));
            $this->model->setKey(Mage::getStoreConfig('payment/magefm_moip/access_key'));
        }

        return $this->model;
    }

    protected function getEnvironment()
    {
        if (Mage::getStoreConfigFlag('payment/magefm_moip/testing')) {
            return 'https://desenvolvedor.moip.com.br/sandbox';
        }

        return 'https://www.moip.com.br';
    }

    /**
     * This method calculates the interest that it is passed for the customer to pau
     * @param $order
     */
    protected function calculateInterest($total, $numeroParcelas)
    {
        if($numeroParcelas == 1) {
            return $total;
        }elseif($numeroParcelas > 1 && $numeroParcelas <= 6) {
            return $total * (1.002);
        } else {
            return $total * (1.004);
        }
    }


    public function calculateOrderTotalWithInterest($total, $numeroParcelas)
    {
        $total = $this->calculateInterest($total, $numeroParcelas);

        switch ($numeroParcelas) {
            case 1:
                return $total;
            case 2:
                $taxa = 1.25;
                break;
            case 3:
                $taxa = 2.49;
                break;
            case 4:
                $taxa = 3.74;
                break;
            case 5:
                $taxa = 4.98;
                break;
            case 6:
                $taxa = 6.23;
                break;
            case 7:
                $taxa = 7.47;
                break;
            case 8:
                $taxa = 8.72;
                break;
            case 9:
                $taxa = 9.96;
                break;
            case 10:
                $taxa = 11.21;
                break;
            case 11:
                $taxa = 12.45;
                break;
            case 12:
                $taxa = 13.70;
                break;
            default:
                Mage::throwException(Mage::helper('magefm_moip')->__('Número de parcelas inválido'));
        }

        return ($total / ((100 - $taxa) / 100));
    }

}
