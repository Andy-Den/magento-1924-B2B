<?php

if(Mage::helper('core')->isModuleEnabled('FVets_CheckoutSplit')){
	class Idev_OneStepCheckout_Model_Sales_Quote_Tmp extends FVets_CheckoutSplit_Model_Sales_Quote {}
} else {
	class Idev_OneStepCheckout_Model_Sales_Quote_Tmp extends Mage_Sales_Model_Quote {}
}

class  Idev_OneStepCheckout_Model_Sales_Quote extends Idev_OneStepCheckout_Model_Sales_Quote_Tmp
{

    /**
     * Collect totals patched for magento issue #26145
     *
     * @return Mage_Sales_Model_Quote
     */
    public function collectTotals()
    {

        /**
         * patch for magento issue #26145
         */
        if (!$this->getTotalsCollectedFlag()) {

            $items = $this->getAllItems();

            foreach($items as $item){
                $item->setData('calculation_price', null);
                $item->setData('original_price', null);
            }

        }

        parent::collectTotals();
        return $this;

    }

	public function getItemsCollection($useCache = true)
	{
		if ($this->hasItemsCollection()) {
			return $this->getData('items_collection');
		}
		if (is_null($this->_items)) {
			$this->_items = Mage::getModel('sales/quote_item')->getCollection()->addOrder('name', 'asc');
			$this->_items->setQuote($this);
		}
		return $this->_items;
	}

}