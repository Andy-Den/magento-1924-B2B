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
 * @package     Innoexts_WarehousePlus
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Warehouse plus data helper
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Helper_Directory_Currency 
    extends Innoexts_Warehouse_Helper_Directory_Currency 
{
    /**
     * Base currency
     * 
     * @var Mage_Directory_Model_Currency
     */
    protected $_base;
    /**
     * Get base currency code
     * 
     * @return string
     */
    public function getBaseCode()
    {
        return Mage::app()->getBaseCurrencyCode();
    }
    /**
     * Get base currency
     * 
     * @return Mage_Directory_Model_Currency
     */
    public function getBase()
    {
        if (is_null($this->_base)) {
            $this->_base = $this->getCurrency()->load($this->getBaseCode());
        }
        return $this->_base;
    }
    /**
     * Get current currency
     * 
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrent()
    {
        return $this->getWarehouseHelper()->getCurrentStore()->getCurrentCurrency();
    }
    /**
     * Get current store base currency
     * 
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrentStoreBase()
    {
        return $this->getWarehouseHelper()->getCurrentStore()->getBaseCurrency();
    }
    /**
     * Get current store base currency code
     * 
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrentStoreBaseCode()
    {
        return $this->getCurrentStoreBase()->getCode();
    }
    /**
     * Get currency rate
     * 
     * @param string $fromCurrency
     * @param string $toCurrency
     * 
     * @return float
     */
    public function getRate($fromCode, $toCode)
    {
        $rate = $this->getCurrency()->load($fromCode)->getRate($toCode);
        if (!$rate) {
            $rate = $this->getCurrency()->load($toCode)->getRate($fromCode);
            if (!$rate) {
                $baseCurrency   = $this->getBase();
                $fromRate       = $baseCurrency->getRate($fromCode);
                $toRate         = $baseCurrency->getRate($toCode);
                if (!$fromRate) {
                    $fromRate       = 1;
                }
                if (!$toRate) {
                    $toRate         = 1;
                }
                $rate = $toRate / $fromRate;
            } else {
                $rate = 1 / $rate;
            }
        }
        return $rate;
    }
    /**
     * Get website base currency codes
     * 
     * @return array
     */
    public function getBaseCodes()
    {
        $codes = array();
        foreach ($this->getWarehouseHelper()->getWebsites() as $websiteId => $website) {
            $codes[$websiteId] = $website->getBaseCurrencyCode();
        }
        return $codes;
    }
}