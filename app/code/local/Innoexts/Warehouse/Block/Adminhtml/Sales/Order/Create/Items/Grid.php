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
 * Order create items grid
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Sales_Order_Create_Items_Grid 
    extends Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid 
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
     * Get subtotal
     * 
     * @return float
     */
    public function getSubtotal()
    {
        $helper     = $this->getWarehouseHelper();
        $config     = $helper->getConfig();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled()) {
            $subtotal = 0;
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                if ($this->displayTotalsIncludeTax()) {
                    
                    if ($helper->getVersionHelper()->isGe1800()) {
                        if ($this->getIsPriceInclTax()) {
                            $subtotal += $address->getSubtotalInclTax();
                        } else {
                            $subtotal += $address->getSubtotal() + $address->getTaxAmount();
                        }
                    } else {
                        if ($address->getSubtotalInclTax()) {
                            $subtotal += $address->getSubtotalInclTax();
                        } else {
                            $subtotal += $address->getSubtotal() + $address->getTaxAmount();
                        }
                    }
                    
                } else {
                    
                    if ($helper->getVersionHelper()->isGe1800()) {
                        
                        if ($this->getIsPriceInclTax()) {
                            return $address->getSubtotalInclTax() - $address->getTaxAmount();
                        } else {
                            return $address->getSubtotal();
                        }
                        
                    } else {
                        $subtotal += $address->getSubtotal();
                    }
                    
                }
            }
            return $subtotal;
        } else {
            return parent::getSubtotal();
        }
    }
    /**
     * Get discount
     * 
     * @return float
     */
    public function getDiscountAmount()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled()) {
            $discount = 0;
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $discount += $address->getDiscountAmount();
            }
            return $discount;
        } else {
            return parent::getDiscountAmount();
        }
    }
    /**
     * Get subtotal with discount
     * 
     * @return float
     */
    public function getSubtotalWithDiscount()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled()) {
            return $this->getSubtotal() + $this->getDiscountAmount();
        } else {
            return parent::getSubtotalWithDiscount();
        }
    }
}