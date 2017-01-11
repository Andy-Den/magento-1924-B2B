<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Paypal Express checkout
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */

class Innoexts_Warehouse_Model_Paypal_Express_Checkout_Abstract 
        extends Mage_Paypal_Model_Express_Checkout 
{
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    protected function getVersionHelper()
    {
        return $this->getWarehouseHelper()
            ->getVersionHelper();
    }
    /**
     * Initialize checkout
     * 
     * @return Mage_Paypal_Model_Express_Checkout
     */
    protected function _init()
    {
        $quote                  = $this->_quote;
        $quote->reapplyStocks();
        $quote->getShippingAddress()
            ->setCollectShippingRates(true);
        $quote->setTotalsCollectedFlag(false);
        $quote->collectTotals();
        return $this;
    }
    /**
     * Update quote when returned from PayPal
     * rewrite billing address by paypal
     * save old billing address for new customer
     * export shipping address in case address absence
     *
     * @param string $token
     */
    public function returnFromPaypal($token)
    {
        $this->_init();
        return parent::returnFromPaypal($token);
    }
    /**
     * Check whether order review has enough data to initialize
     *
     * @param $token
     * 
     * @throws Mage_Core_Exception
     */
    public function prepareOrderReview($token = null)
    {
        $this->_init();
        return parent::prepareOrderReview($token);
    }
}


if (Mage::helper('innoexts_core')->getVersionHelper()->isGe1900()) {
    class Innoexts_Warehouse_Model_Paypal_Express_Checkout 
        extends Innoexts_Warehouse_Model_Paypal_Express_Checkout_Abstract 
    {
        /**
         * Reserve order ID for specified quote and start checkout on PayPal
         *
         * @param string $returnUrl
         * @param string $cancelUrl
         * @param bool|null $button
         * 
         * @return mixed
         */
        public function start($returnUrl, $cancelUrl, $button = null)
        {
            $this->_init();
            return parent::start($returnUrl, $cancelUrl, $button);
        }
    }
} else {
    class Innoexts_Warehouse_Model_Paypal_Express_Checkout 
        extends Innoexts_Warehouse_Model_Paypal_Express_Checkout_Abstract 
    {
        /**
         * Reserve order ID for specified quote and start checkout on PayPal
         *
         * @param string $returnUrl
         * @param string $cancelUrl
         * 
         * @return mixed
         */
        public function start($returnUrl, $cancelUrl)
        {
            $this->_init();
            return parent::start($returnUrl, $cancelUrl);
        }
    }
}


