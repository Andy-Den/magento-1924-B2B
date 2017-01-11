<?php

class MageFM_MoIP_Block_Form_Creditcard extends Mage_Payment_Block_Form_Cc
{

    protected function _construct()
    {
        $this->setTemplate('magefm/moip/creditcard.phtml');
    }

    public function getInstallments()
    {
        $total = $this->getMethod()->getInfoInstance()->getQuote()->getGrandTotal();
        $minimumAmount = (float) $this->getMethod()->getConfigData('installments_minimum_amount');
        $maximumNumber = (int) $this->getMethod()->getConfigData('installments_maximum_number');

        $installments = array(1 => $this->__('À vista'));

        for ($i = 2; $i <= $maximumNumber; $i++) {
            $installmentTotal = Mage::helper('magefm_moip')->calculateOrderTotalWithInterest($total, $i);
            $amount = ceil($installmentTotal / $i * 100) / 100;

            if ($amount < $minimumAmount) {
                return $installments;
            }

            $installments[$i] = $this->__('%d installments of %.2f (with interest)', $i, $amount);
        }

        return $installments;
    }

    protected function _calculateInstallmentAmount($i, $total, $rate = null)
    {
        if (is_null($rate)) {
            return round($total / $i, 2);
        }

        $interest = pow(($rate / 100) + 1, $i);
        $amount = ($total * $interest) / $i;

        return $amount;
    }

}
