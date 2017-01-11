<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Admin source model for Payment Methods
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Model_Condition_Attribute_Source_Paymentmethods extends Mage_Eav_Model_Entity_Attribute_Source_Table
{

    private $modularizedPaymentMethods = array(
        'gwap_boleto',
        'gwap_cc',
        'gwap_deposito'
    );

    /**
     * get possible values
     *
     * @access public
     * @param bool $withEmpty
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $options =  $this->getModularizedPaymentMothods();

        if ($withEmpty) {
            array_unshift($options, array('label'=>'', 'value'=>''));
        }
        return $options;
    }

    /**
     * get active payment methods
     *
     * @access public
     * @return array()
     * @author http://inchoo.net/magento/magento-how-to-get-all-active-payment-modules/
     */
    public function getActivePaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;

    }

    /**
     * get all payment methods
     *
     * @access public
     * @return array()
     */
    public function getAllPaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getAllMethods();

        foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;

    }

    /**
     * get modularized payment methods
     * if a method is ok to use in website
     *
     * @access public
     * @return array()
     */
    public function getModularizedPaymentMothods()
    {
        $payments = Mage::getSingleton('payment/config')->getAllMethods();
        foreach ($payments as $paymentCode=>$paymentModel) {
            if (in_array($paymentCode, $this->modularizedPaymentMethods)) {
                $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
                $methods[$paymentCode] = array(
                    'label' => $paymentTitle,
                    'value' => $paymentCode,
                );
            }
        }

        return $methods;
    }


    /**
     * get options as array
     *
     * @access public
     * @param bool $withEmpty
     * @return string
     */
    public function getOptionsArray($withEmpty = true)
    {
        $options = array();
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * get option text
     *
     * @access public
     * @param mixed $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getOptionsArray();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        $texts = array();
        foreach ($value as $v) {
            if (isset($options[$v])) {
                $texts[] = $options[$v];
            }
        }
        return implode(', ', $texts);
    }
}
