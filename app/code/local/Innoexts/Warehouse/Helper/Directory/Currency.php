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
 * Currency helper
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Helper_Directory_Currency 
    extends Mage_Core_Helper_Abstract 
{
    /**
     * Сodes
     * 
     * @var array
     */
    protected $_сodes;
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
     * Get currency
     * 
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrency()
    {
        return Mage::getModel('directory/currency');
    }
    /**
     * Get currency codes
     * 
     * @return array
     */
    public function getCodes()
    {
        if (is_null($this->_сodes)) {
            $_сodes = $this->getCurrency()->getConfigAllowCurrencies();
            sort($_сodes);
            if (count($_сodes)) {
                $codes = array();
                foreach ($_сodes as $code) {
                    $code = strtoupper($code);
                    $codes[$code] = $code;
                }
                $this->_сodes   = $codes;
            }
        }
        return $this->_сodes;
    }
    /**
     * Get current currency code
     * 
     * @return string
     */
    public function getCurrentCode()
    {
        return $this->getWarehouseHelper()->getCurrentStore()->getCurrentCurrencyCode();
    }
}