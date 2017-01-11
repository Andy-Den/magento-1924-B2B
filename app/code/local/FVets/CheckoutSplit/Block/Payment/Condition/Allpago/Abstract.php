<?php

class FVets_CheckoutSplit_Block_Payment_Condition_Allpago_Abstract extends Mage_Core_Block_Template
{
    protected $_checkoutSession = null;
    protected $_quote = null;
    protected $_store = null;
    protected $_customer = null;

    protected function _construct()
    {
        parent::_construct();
    }

    public function getConditions()
    {
        $collection = mage::getModel('fvets_payment/condition')->getCollection()
            ->addFieldToFilter('payment_methods', array('finset' => array($this->getParentBlock()->getMethodCode())))
            ->addFieldToFilter('price_range_begin', array('lteq' => $this->getGrandTotal()))
            ->addFieldToFilter(
                array('price_range_end', 'price_range_end'),
                array(
                    array('gteq' => $this->getGrandTotal()),
                    array('price_range_end', 'eq' => '0')
                )
            )
            ->addFieldToFilter('status', '1')
            ->addStoreFilter($this->getStore(), false)
            ->addCustomerFilter($this->getCustomer())
            ->addCategoriesFilter(array_keys(Mage::helper('fvets_payment/category')->getQuoteCategories()), true)
            ->excludeCategoriesFilter(array_keys(Mage::helper('fvets_payment/category')->getQuoteCategories()));

        $collection->getSelect()->group('main_table.entity_id');

        $collection->getSelect()->order(['main_table.name','related_customer.position','main_table.price_range_begin ASC']);
        //$collection->getSelect()->order('main_table.price_range_begin ASC');
        
        if (Mage::getSingleton('checkout/session')->getIsSplitCheckout()) {
            Mage::getSingleton('checkout/session')->setFvetsQuoteConditions($collection->getFirstItem()->getId());
        }

        return $collection;
    }

    protected function getCheckoutSession()
    {
        if (!$this->_checkoutSession) {
            $this->_checkoutSession = Mage::getModel('checkout/session');
        }

        return $this->_checkoutSession;
    }

    protected function getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->getCheckoutSession()->getQuote();
        }

        return $this->_quote;
    }

    protected function getStore()
    {
        if (!$this->_store) {
            $this->_store = Mage::app()->getStore();
        }

        return $this->_store;
    }

    protected function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        return $this->_customer;
    }

    protected function getGrandTotal()
    {
        $value = 0;
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            $value += $item->getRowTotal() - $item->getDiscountAmount();
        }
        return $value;
    }
}