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
 * Shipping row renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Tax_Checkout_Shipping 
    extends Mage_Tax_Block_Checkout_Shipping 
{
    /**
     * Get warehouse helper
     *
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get shipping amount include tax
     *
     * @return float
     */
    public function getShippingIncludeTax()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled()) {
            $shippingAmount = 0;
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $shippingAmount += $address->getShippingInclTax();
            }
            return $shippingAmount;
        } else {
            return parent::getShippingIncludeTax();
        }
    }
    /**
     * Get shipping amount exclude tax
     *
     * @return float
     */
    public function getShippingExcludeTax()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled()) {
            $shippingAmount = 0;
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $shippingAmount += $address->getShippingAmount();
            }
            return $shippingAmount;
        } else {
            return parent::getShippingExcludeTax();
        }
    }
    /**
     * Get label for shipping include tax
     *
     * @return float
     */
    public function getIncludeTaxLabel()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled()) {
            $labels = array();
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $labels[$address->getShippingMethod()] = $address->getShippingDescription();
            }
            return $this->escapeHtml($this->helper('tax')->__('Shipping Incl. Tax (%s)', implode(' & ', $labels)));
        } else {
            return parent::getIncludeTaxLabel();
        }
    }
    /**
     * Get label for shipping exclude tax
     * 
     * @return float
     */
    public function getExcludeTaxLabel()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled()) {
            $labels = array();
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $labels[$address->getShippingMethod()] = $address->getShippingDescription();
            }
            return $this->escapeHtml($this->helper('tax')->__('Shipping Excl. Tax (%s)', implode(' & ', $labels)));
        } else {
            return parent::getExcludeTaxLabel();
        }
    }
}