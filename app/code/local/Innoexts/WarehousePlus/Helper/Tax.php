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
 * Tax helper
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Helper_Tax 
    extends Mage_Tax_Helper_Data 
{
    /**
     * Get product price with all tax settings processing
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   float $price
     * @param   bool $includingTax
     * @param   null|Mage_Customer_Model_Address $shippingAddress
     * @param   null|Mage_Customer_Model_Address $billingAddress
     * @param   null|int $ctc
     * @param   null|Mage_Core_Model_Store $store
     * @param   bool $priceIncludesTax
     * 
     * @return  float
     */
    public function getPrice(
        $product, 
        $price, 
        $includingTax = null, 
        $shippingAddress = null, 
        $billingAddress = null,
        $ctc = null, 
        $store = null, 
        $priceIncludesTax = null, 
        $roundPrice = true
    )
    {
        if (!$price) {
            return $price;
        }
        $store                  = $this->_app->getStore($store);
        if (!$this->needPriceConversion($store)) {
            return $price;
        }
        return parent::getPrice(
            $product, 
            $price, 
            $includingTax, 
            $shippingAddress, 
            $billingAddress, 
            $ctc, 
            $store, 
            $priceIncludesTax, 
            $roundPrice
        );
    }
}