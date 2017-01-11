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
 * Product collection
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Model_Mysql4_Catalog_Product_Collection 
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection 
{
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_WarehousePlus_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Add tier price data to loaded items
     *
     * @return Innoexts_WarehousePlus_Model_Mysql4_Catalog_Product_Collection
     */
    public function addTierPriceData()
    {
        $helper             = $this->getWarehouseHelper();
        $priceHelper        = $helper->getProductPriceHelper();
        if ($this->getFlag('tier_price_added')) {
            return $this;
        }
        $tierPrices = array();
        $productIds = array();
        foreach ($this->getItems() as $item) {
            $productIds[] = $item->getId();
            $tierPrices[$item->getId()] = array();
        }
        if (!count($productIds)) {
            return $this;
        }
        $store              = $helper->getStoreById($this->getStoreId());
        $websiteId          = null;
        $storeId            = null;
        if ($priceHelper->isGlobalScope()) {
            $websiteId          = null;
            $storeId            = null;
        } else if ($priceHelper->isWebsiteScope() && $store->getId()) {
            $websiteId          = $helper->getWebsiteIdByStoreId($store->getId());
            $storeId            = null;
        } else if ($priceHelper->isStoreScope() && $store->getId()) {
            $websiteId          = $helper->getWebsiteIdByStoreId($store->getId());
            $storeId            = $store->getId();
        }
        $currency       = $store->getCurrentCurrencyCode();
        $adapter        = $this->getConnection();
        $columns        = array(
            'price_id'      => 'value_id', 
            'website_id'    => 'website_id', 
            'store_id'      => 'store_id', 
            'stock_id'      => 'stock_id', 
            'all_groups'    => 'all_groups', 
            'cust_group'    => 'customer_group_id', 
            'price_qty'     => 'qty', 
            'price'         => 'value', 
            'product_id'    => 'entity_id', 
            'currency'      => 'currency', 
        );
        $select  = $adapter->select()
            ->from($this->getTable('catalog/product_attribute_tier_price'), $columns)
            ->where('entity_id IN(?)', $productIds)
            ->order(array('entity_id','qty'));
        if ($websiteId == '0') {
            $select->where('website_id = ?', $websiteId);
        } else {
            $select->where('website_id IN(?)', array('0', $websiteId));
        }
        if ($storeId == '0') {
            $select->where('store_id = ?', $storeId);
        } else {
            $select->where('store_id IN(?)', array('0', $storeId));
        }
        if (!is_null($currency)) {
            if ($currency == '') {
                $select->where("(currency IS NULL) OR (currency = '')", $currency);
            } else {
                $select->where("(currency = ?) OR (currency IS NULL) OR (currency = '')", $currency);
            }
        }
        foreach ($adapter->fetchAll($select) as $row) {
            $tierPrices[$row['product_id']][] = array(
                'website_id'     => $row['website_id'], 
                'store_id'       => $row['store_id'], 
                'stock_id'       => $row['stock_id'], 
                'cust_group'     => $row['all_groups'] ? Mage_Customer_Model_Group::CUST_GROUP_ALL : $row['cust_group'], 
                'price_qty'      => $row['price_qty'], 
                'price'          => $row['price'], 
                'website_price'  => $row['price'], 
                'currency'       => (isset($row['currency']) && $row['currency']) ? $row['currency'] : null, 
            );
        }
        foreach ($this->getItems() as $item) {
            $data = $tierPrices[$item->getId()];
            $item->setTierPrices($data);
            $priceHelper->setTierPrice($item);
        }
        $this->setFlag('tier_price_added', true);
        return $this;
    }
}