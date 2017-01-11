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
class Innoexts_WarehousePlus_Helper_Data 
    extends Innoexts_Warehouse_Helper_Data 
{
    /**
     * Websites
     * 
     * @var array of Mage_Core_Model_Website
     */
    protected $_websites;
    /**
     * Get websites
     * 
     * @return array of Mage_Core_Model_Website
     */
    public function getWebsites()
    {
        if (is_null($this->_websites)) {
            $this->_websites = Mage::app()->getWebsites();
        }
        return $this->_websites;
    }
    /**
     * Get website by identifier
     * 
     * @param mixed $websiteId
     * 
     * @return Mage_Core_Model_Website
     */
    public function getWebsiteById($websiteId)
    {
        return Mage::app()->getWebsite($websiteId);
    }
    /**
     * Get default store by store identifier
     * 
     * @param mixed $storeId
     * 
     * @return int
     */
    public function getDefaultStoreByStoreId($storeId)
    {
        return $this->getWebsiteByStoreId($storeId)->getDefaultStore();
    }
    /**
     * Get default store identifier by store identifier
     * 
     * @param mixed $storeId
     * 
     * @return int
     */
    public function getDefaultStoreIdByStoreId($storeId)
    {
        return $this->getDefaultStoreByStoreId($storeId)->getId();
    }
    /**
     * Get current store identifier
     * 
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->getCurrentStore()->getId();
    }
    /**
     * Check if single store mode is in effect
     * 
     * @return bool 
     */
    public function isSingleStoreMode()
    {
        return Mage::app()->isSingleStoreMode();
    }
    /**
     * Get store identifier
     * 
     * @param int $currentStoreId
     * 
     * @return int
     */
    public function getStoreId($currentStoreId)
    {
        $storeId = null;
        $priceHelper = $this->getProductPriceHelper();
        if ($priceHelper->isStoreScope()) {
            $storeId = $currentStoreId;
        } else if ($priceHelper->isWebsiteScope()) {
            $storeId = $this->getDefaultStoreIdByStoreId($currentStoreId);
        } else {
            $storeId = 0;
        }
        return $storeId;
    }
}