<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Core
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core helper
 * 
 * @category   Innoexts
 * @package    Innoexts_Core
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Core_Helper_Data 
    extends Mage_Core_Helper_Abstract 
{
    /**
     * Websites
     * 
     * @var array of Mage_Core_Model_Website
     */
    protected $_websites;
    /**
     * Stores
     * 
     * @var array of Mage_Core_Model_Store
     */
    protected $_stores;
    /**
     * Get core helper
     * 
     * @return Mage_Core_Helper_Data
     */
    public function getCoreHelper()
    {
        return Mage::helper('core');
    }
    /**
     * Get string helper
     * 
     * @return Mage_Core_Helper_String
     */
    public function getStringHelper()
    {
        return Mage::helper('core/string');
    }
    /**
     * Get Http helper
     * 
     * @return Mage_Core_Helper_Http
     */
    public function getHttpHelper()
    {
        return Mage::helper('core/http');
    }
    /**
     * Get locale helper
     * 
     * @return Mage_Core_Model_Locale
     */
    public function getLocaleHelper()
    {
        return Mage::app()->getLocale();
    }
    /**
     * Get address helper
     * 
     * @return Innoexts_Core_Helper_Address
     */
    public function getAddressHelper()
    {
        return Mage::helper('innoexts_core/address');
    }
    /**
     * Get customer helper
     * 
     * @return Innoexts_Core_Helper_Customer
     */
    public function getCustomerHelper()
    {
        return Mage::helper('innoexts_core/customer');
    }
    /**
     * Get checkout helper
     * 
     * @return Innoexts_Core_Helper_Checkout
     */
    public function getCheckoutHelper()
    {
        return Mage::helper('innoexts_core/checkout');
    }
    /**
     * Get database helper
     * 
     * @return Innoexts_Core_Helper_Database
     */
    public function getDatabaseHelper()
    {
        return Mage::helper('innoexts_core/database');
    }
    /**
     * Get directory helper
     * 
     * @return Innoexts_Core_Helper_Directory
     */
    public function getDirectoryHelper()
    {
        return Mage::helper('innoexts_core/directory');
    }
    /**
     * Get math helper
     * 
     * @return Innoexts_Core_Helper_Math
     */
    public function getMathHelper()
    {
        return Mage::helper('innoexts_core/math');
    }
    /**
     * Get model helper
     * 
     * @return Innoexts_Core_Helper_Model
     */
    public function getModelHelper()
    {
        return Mage::helper('innoexts_core/model');
    }
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    public function getVersionHelper()
    {
        return Mage::helper('innoexts_core/version');
    }
    /**
     * Get core session
     * 
     * @return Mage_Core_Model_Session
     */
    public function getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }
    /**
     * Get admin session
     * 
     * @return Mage_Admin_Model_Session
     */
    public function getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }
    /**
     * Check if admin store is active
     * 
     * @return boolean
     */
    public function isAdmin()
    {
        return Mage::app()->getStore()->isAdmin();
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
     * Get full controller name
     * 
     * @return string
     */
    public function getFullControllerName()
    {
        $request = $this->getRequest();
        return $request->getRouteName().'_'.$request->getControllerName();
    }
    /**
     * Check if create order request is active
     * 
     * @return bool
     */
    public function isCreateOrderRequest()
    {
        if ($this->isAdmin()) {
            $controllerName = $this->getRequest()->getControllerName();
            if (in_array(strtolower($controllerName), array('sales_order_edit', 'sales_order_create'))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
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
     * Get stores
     * 
     * @return array of Mage_Core_Model_Store
     */
    public function getStores()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores();
        }
        return $this->_stores;
    }
    /**
     * Get website
     * 
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        return Mage::app()->getWebsite();
    }
    /**
     * Get website id
     * 
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getWebsite()->getId();
    }
    /**
     * Get website by id
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
     * Get website by store id
     * 
     * @param mixed $storeId
     * 
     * @return Mage_Core_Model_Website 
     */
    public function getWebsiteByStoreId($storeId)
    {
        return $this->getStoreById($storeId)->getWebsite();
    }
    /**
     * Get website id by store id
     * 
     * @param mixed $storeId
     * 
     * @return int
     */
    public function getWebsiteIdByStoreId($storeId)
    {
        return $this->getStoreById($storeId)->getWebsiteId();
    }
    /**
     * Get website ids
     * 
     * @return array
     */
    public function getWebsiteIds()
    {
        return array_keys($this->getWebsites());
    }
    /**
     * Get website array
     * 
     * @return array
     */
    public function getWebsiteArray()
    {
        $array = array();
        foreach ($this->getWebsites() as $website) {
            $array[(int) $website->getId()] = $website->getName();
        }
        return $array;
    }
    /**
     * Get store
     * 
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }
    /**
     * Get store id
     * 
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }
    /**
     * Get store by id
     * 
     * @param mixed $storeId
     * 
     * @return Mage_Core_Model_Store
     */
    public function getStoreById($storeId)
    {
        return Mage::app()->getStore($storeId);
    }
    /**
     * Get default store by store id
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
     * Get stores ids
     * 
     * @return array
     */
    public function getStoreIds()
    {
        return array_keys($this->getStores());
    }
    /**
     * Get store identifiers by website identifier
     * 
     * @param mixed $websiteId
     * 
     * @return array
     */
    public function getStoreIdsByWebsiteId($websiteId)
    {
        return $this->getWebsiteById($websiteId)->getStoreIds();
    }
    /**
     * Get current store
     * 
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        if ($this->isAdmin() && $this->isCreateOrderRequest()) {
            return Mage::getSingleton('adminhtml/session_quote')->getStore();
        } else {
            return Mage::app()->getStore();
        }
    }
    /**
     * Get current store id
     * 
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->getCurrentStore()->getId();
    }
    /**
     * Get table
     * 
     * @param string $entityName
     * 
     * @return string 
     */
    public function getTable($entityName)
    {
        return $this->getDatabaseHelper()->getTable($entityName);
    }
    /**
     * Encode the mixed value
     *
     * @param mixed $valueToEncode
     * @param boolean $cycleCheck
     * @param array $options
     * 
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array())
    {
        return $this->getCoreHelper()->jsonEncode($valueToEncode, $cycleCheck, $options);
    }
    /**
     * Decodes the string
     *
     * @param string $encodedValue
     * 
     * @return mixed
     */
    public function jsonDecode($encodedValue, $objectDecodeType = Zend_Json::TYPE_ARRAY)
    {
        return $this->getCoreHelper()->jsonDecode($encodedValue, $objectDecodeType);
    }
    /**
     * Get request
     * 
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest() 
    {
        return Mage::app()->getRequest();
    }
    /**
     * Compare options
     * 
     * @param array $option1
     * @param array $option1
     * 
     * @return int
     */
    public function compareOptions($option1, $option2)
    {
        $value1 = (isset($option1['label'])) ?$option1['label'] : '';
        $value2 = (isset($option2['label'])) ?$option2['label'] : '';
        if ($value1 != $value2) {
            return $value1 < $value2 ? -1 : 1;
        }
        return 0;
    }
    /**
     * Sort Options
     * 
     * @param array $options
     * 
     * @return Innoexts_Core_Helper_Data
     */
    public function sortOptions(&$options)
    {
        usort($options, array($this, 'compareOptions'));
        foreach ($options as $optionIndex => $option) {
            if (isset($option['value']) && is_array($option['value'])) {
                $this->sortOptions($options[$optionIndex]['value']);
            }
        }
        return $this;
    }
    /**
     * Prepare options
     * 
     * @param array $options
     * @param bool $required
     * @param string $emptyLabel
     * @param string $emptyValue
     * 
     * @return Innoexts_Core_Helper_Data
     */
    public function prepareOptions(&$options, $required = true, $emptyLabel = '', $emptyValue = '')
    {
        $this->sortOptions($options);
        if (!$required) {
            array_unshift(
                $options, 
                array(
                    'value' => $emptyValue, 
                    'label' => $emptyLabel
                )
            );
        }
        return $this;
    }
    /**
     * Get store options
     * 
     * @param bool $required
     * @param bool $padding
     * @param string $emptyLabel
     * @param string $emptyValue
     * 
     * @return array
     */
    public function getStoreOptions($required = true, $padding = false, $emptyLabel = '', $emptyValue = '')
    {
        $nonEscapableNbspChar   = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $options                = array();
        foreach ($this->getWebsites() as $website) {
            $websiteId = $website->getId();
            if (!isset($options[$websiteId])) {
                $options[$websiteId] = array(
                    'value' => array(), 
                    'label' => $website->getName(), 
                );
            }
            foreach ($website->getGroups() as $storeGroup) {
                $storeGroupId   = $storeGroup->getId();
                $storeGroupName = (($padding) ? str_repeat($nonEscapableNbspChar, 4) : '').$storeGroup->getName();
                if (!isset($options[$websiteId]['value'][$storeGroupId])) {
                    $options[$websiteId]['value'][$storeGroupId] = array(
                        'value' => array(), 
                        'label' => $storeGroupName, 
                    );
                }
                foreach ($storeGroup->getStores() as $store) {
                    $storeId    = $store->getId();
                    $storeName  = (($padding) ? str_repeat($nonEscapableNbspChar, 4) : '').$store->getName();
                    $options[$websiteId]['value'][$storeGroupId]['value'][$storeId] = array(
                        'value' => $storeId, 
                        'label' => $storeName, 
                    );
                }
            }
        }
        $this->prepareOptions($options, $required, $emptyLabel, $emptyValue);
        return $options;
    }
}