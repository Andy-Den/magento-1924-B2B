<?php

require_once 'Zend/Log.php';

/**
 * Allpago - Gwap Payment Module
 *
 * @title      Magento -> Custom Payment Module for Gwap
 * @category   Payment Gateway
 * @package    Allpago_Gwap
 * @author     Allpago Development Team
 * @copyright  Copyright (c) 2013 Allpago
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Allpago_Gwap_Model_Methods_Deposito extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';
    const PAYMENT_TYPE_SALE = 'SALE';

    protected $_code = 'gwap_deposito';
    protected $_formBlockType = 'gwap/form_deposito';
    protected $_infoBlockType = 'gwap/info_deposito';
    protected $_allowCurrencyCode = array('BRL', 'USD');
    protected $_canSaveCc = false;
    protected $_canCapture = true;

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $info = $this->getInfoInstance();
        $info->setAdditionalInformation('GwapDepositoType', $data->getGwapDepositoType());

        return $this;
    }

    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    public function getConfig()
    {
        return new Varien_Object(Mage::getStoreConfig('payment/gwap_deposito'));
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    /**
     *  check if capture is available
     *
     * @return bool
     */
    public function canCapture()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock($this->_formBlockType, $name)
            ->setMethod('gwap_deposito')
            ->setPayment($this->getPayment())
            ->setTemplate('gwap/deposito/form.phtml');

        return $block;
    }

    /**
     * Get gwap session namespace
     *
     * @return Allpago_Gwap_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('gwap/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        return $this;
    }

    /*
     * Validate
     */

    public function validate()
    {
        parent::validate();
    }

}
