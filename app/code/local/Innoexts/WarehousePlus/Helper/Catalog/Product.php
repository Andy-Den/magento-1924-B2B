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
 * Product helper
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Helper_Catalog_Product 
    extends Innoexts_Warehouse_Helper_Catalog_Product 
{
    /**
     * Get store id by store id
     * 
     * @param int $storeId
     * 
     * @return int 
     */
    public function getStoreIdByStoreId($storeId)
    {
        $_storeId       = null;
        $helper         = $this->getWarehouseHelper();
        $priceHelper    = $this->getPriceHelper();
        if ($priceHelper->isStoreScope()) {
            $_storeId       = $storeId;
        } else if ($priceHelper->isWebsiteScope()) {
            $_storeId       = $helper->getDefaultStoreIdByStoreId($storeId);
        } else {
            $_storeId       = 0;
        }
        return $_storeId;
    }
    /**
     * Get store id
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return int
     */
    public function getStoreId($product)
    {
        return $this->getStoreIdByStoreId((int) $product->getStoreId());
    }
}