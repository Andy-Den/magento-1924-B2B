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
 * Shipping total
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Sales_Quote_Address_Total_Shipping 
    extends Mage_Sales_Model_Quote_Address_Total_Shipping 
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
     * Add shipping totals information to address object
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * 
     * @return Innoexts_Warehouse_Model_Sales_Quote_Address_Total_Shipping
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if ($config->isMultipleMode() && $config->isSplitOrderEnabled()) {
            $amount = $address->getShippingAmount();
            if ($amount != 0 || $address->getShippingDescription()) {
                $title = Mage::helper('sales')->__('Shipping & Handling');
                $address->addTotal(array('code' => $this->getCode(), 'title' => $title, 'value' => $address->getShippingAmount()));
            }
            return $this;
        } else {
            parent::fetch($address);
            return $this;
        }
    }
}