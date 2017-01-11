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
 * Grand total row renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Tax_Checkout_Grandtotal 
    extends Mage_Tax_Block_Checkout_Grandtotal 
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
     * Get grandtotal exclude tax
     * 
     * @return float
     */
    public function getTotalExclTax()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled() && $this->getQuote()->getShippingAddress()) {
            $totalExclTax = 0;
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $totalExclTax += ($address->getGrandTotal() - $address->getTaxAmount());
            }
            return max($totalExclTax, 0);
        } else {
            return parent::getTotalExclTax();
        }
    }
}