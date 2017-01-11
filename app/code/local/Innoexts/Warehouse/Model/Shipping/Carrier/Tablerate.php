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
 * Shipping carrier table rate
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Shipping_Carrier_Tablerate 
    extends Mage_Shipping_Model_Carrier_Tablerate 
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
     * Collect rates
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * 
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $helper         = $this->getWarehouseHelper();
        
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }
        $freeQty = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeQty += $item->getQty() * ($child->getQty() - (is_numeric($child->getFreeShipping()) ? 
                                $child->getFreeShipping() : 0));
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeQty += ($item->getQty() - (is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0));
                }
            }
        }
        if (!$request->getConditionName()) {
            $request->setConditionName($this->getConfigData('condition_name') ? 
                $this->getConfigData('condition_name') : $this->_default_condition_name);
        }
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();
        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);
        $result = Mage::getModel('shipping/rate_result');
        foreach ($helper->getShippingTablerateMethods() as $tablerateMethod) {
            $request->setMethodId($tablerateMethod->getMethodId());
            $rate = $this->getRate($request);
            if (!empty($rate) && $rate['price'] >= 0) {
                $method = Mage::getModel('shipping/rate_result_method');
                $method->setCarrier('tablerate');
                $method->setCarrierTitle($this->getConfigData('title'));
                $method->setMethod($tablerateMethod->getCode());
                $method->setMethodTitle($tablerateMethod->getName());
                if ($request->getFreeShipping() === true || ($oldQty == $freeQty)) {
                    $shippingPrice = 0;
                } else {
                    $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
                }
                $method->setPrice($shippingPrice);
                $method->setCost($rate['cost']);
                $result->append($method);
            }
        }
        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);
        return $result;
    }
    /**
     * Get allowed shipping methods
     * 
     * @return array
     */
    public function getAllowedMethods()
    {
        $helper     = $this->getWarehouseHelper();
        $methods    = array();
        foreach ($helper->getShippingTablerateMethods() as $tablerateMethod) {
            $methods[$tablerateMethod->getCode()] = $tablerateMethod->getName();
        }
        return $methods;
    }
}