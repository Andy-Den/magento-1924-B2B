
<?php
class FVets_Payment_Block_Order_Totals extends Mage_Sales_Block_Order_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();

        /**
         * Discount Increase Total
         */

        if ((float)$this->getSource()->getPaymentIncreaseAmount() != 0) {
        $i = array_search('grand_total', array_keys($this->_totals));
        $this->_totals = array_slice($this->_totals, 0, $i, true) +
            array('payment_condition_increase' => new Varien_Object(array(
                'code' => 'payment_condition_increase',
                'field' => 'payment_increase_amount',
                'value' => $this->getSource()->getPaymentIncreaseAmount(),
                'label' => $this->__('Payment Condition Increase'),
                'is_formated' => false,
            ))) +
            array_slice($this->_totals, $i, count($this->_totals)-$i, true);
        }

        return $this;
    }
}