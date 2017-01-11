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
 * Product price observer
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Model_Catalog_Product_Price_Observer 
    extends Innoexts_Warehouse_Model_Catalog_Product_Price_Observer 
{
    /**
     * Get product collection final price
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_WarehousePlus_Model_Catalog_Product_Price_Observer
     */
    public function getCollectionFinal($observer)
    {
        $helper                 = $this->getWarehouseHelper();
        $collection             = $observer->getEvent()->getCollection();
        $customerAddress        = $helper->getCustomerLocatorHelper()->getCustomerAddress();
        $zonePriceCollection    = Mage::getSingleton('catalog/product_zone_price')->getCollection();
        $zonePriceCollection->setProductsFilter($collection);
        $zonePriceCollection->setAddressFilter($customerAddress);
        $zonePrices = array();
        foreach ($zonePriceCollection as $zonePrice) {
            $productId = $zonePrice->getProductId();
            if (!isset($zonePrices[$productId])) {
                $zonePrices[$productId] = $zonePrice;
            }
        }
        if (count($zonePrices)) {
            foreach ($collection as $product) {
                $productId = $product->getId();
                if (isset($zonePrices[$productId])) {
                    $zonePrice      = $zonePrices[$productId];
                    $finalPrice     = (float) $product->getFinalPrice();
                    $finalPrice     = $zonePrice->getFinalPrice($finalPrice);
                    $product->setFinalPrice($finalPrice);
                }
            }
        }
        return $this;
    }
    /**
     * Get product final price
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_WarehousePlus_Model_Catalog_Product_Price_Observer
     */
    public function getFinal($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $helper             = $this->getWarehouseHelper();
            $customerAddress    = $helper->getCustomerLocatorHelper()->getCustomerAddress();
            $zonePrice          = Mage::getModel('catalog/product_zone_price');
            $zonePrice->loadByProductIdAndAddress($product->getId(), $customerAddress);
            if ($zonePrice->getId()) {
                $finalPrice         = (float) $product->getFinalPrice();
                $finalPrice         = $zonePrice->getFinalPrice($finalPrice);
                $product->setFinalPrice($finalPrice);
            }
        }
        return $this;
    }
    /**
     * Prepare product index
     *
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_WarehousePlus_Model_Catalog_Product_Price_Observer
     */
    public function prepareIndex(Varien_Event_Observer $observer)
    {
        $event                   = $observer->getEvent();
        $select                  = clone $event->getSelect();
        $indexTable              = $event->getIndexTable();
        $entityId                = $event->getEntityId();
        $updateFields            = $event->getUpdateFields();
        $resource                = Mage::getSingleton('core/resource');
        $adapter                 = $resource->getConnection('core_write');
        $productZonePriceTable   = $resource->getTableName('catalog/product_zone_price');
        if (empty($updateFields)) {
            return $this;
        }
        if (is_array($indexTable)) {
            foreach ($indexTable as $key => $value) {
                if (is_string($key)) {
                    $indexAlias = $key;
                } else {
                    $indexAlias = $value;
                }
                break;
            }
        } else {
            $indexAlias = $indexTable;
        }
        foreach ($updateFields as $priceField) {
            if ($priceField != 'min_price') {
                continue;
            }
            $priceCond = $adapter->quoteIdentifier(array($indexAlias, $priceField));
            $priceAlias = $priceField.'_pzp';
            $priceCountAlias = $priceAlias.'_count';
            $function = 'MIN';
            $countSelect = $adapter->select()
                ->from(array($priceCountAlias => $productZonePriceTable), 'COUNT(*)')
                ->where($priceCountAlias.'.product_id = '.$entityId);
            $priceSelect = $adapter->select()
                ->from(array($priceAlias => $productZonePriceTable), array())
                ->where($priceAlias.'.product_id = '.$entityId)
                ->columns(new Zend_Db_Expr(
                $function."(IF(".
                    $priceAlias.".price_type = 'fixed', ".
                    "IF(".$priceAlias.".price < {$priceCond}, {$priceCond} - ".$priceAlias.".price, {$priceCond}), ".
                    "IF(".$priceAlias.".price < 100, ROUND({$priceCond} - (".$priceAlias.".price * ({$priceCond} / 100)), 4), {$priceCond})".
                "))"
            ));
            $priceExpr = new Zend_Db_Expr("IF((".$countSelect->assemble().") > 0, (".$priceSelect->assemble()."), {$priceCond})");
            $select->columns(array($priceField => $priceExpr));
        }
        $query = $select->crossUpdateFromSelect($indexTable);
        $adapter->query($query);
        return $this;
    }
}