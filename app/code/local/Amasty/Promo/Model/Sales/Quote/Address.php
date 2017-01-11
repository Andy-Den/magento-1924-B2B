<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (http://www.amasty.com)
 */

if(Mage::helper('core')->isModuleEnabled('Allpago_Installments')){
	class Amasty_Promo_Model_Sales_Quote_Address_Tmp extends Allpago_Installments_Model_Address {}
} else {
	class Amasty_Promo_Model_Sales_Quote_Address_Tmp extends Mage_Sales_Model_Quote_Address {}
}

class Amasty_Promo_Model_Sales_Quote_Address extends Amasty_Promo_Model_Sales_Quote_Address_Tmp
{
    /**
     * Collect address totals
     * Add sales_quote_address_collect_totals_before event for Magento<=1.5
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function collectTotals()
    {
        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_before', array($this->_eventObject => $this));
        foreach ($this->getTotalCollector()->getCollectors() as $model) {
            $model->collect($this);
        }
        Mage::dispatchEvent($this->_eventPrefix . '_collect_totals_after', array($this->_eventObject => $this));
        return $this;
    }
}