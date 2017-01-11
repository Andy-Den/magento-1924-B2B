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
 * @package     Innoexts_ShippingTablerate
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipping tablerate helper
 * 
 * @category   Innoexts
 * @package    Innoexts_ShippingTablerate
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ShippingTablerate_Helper_Data 
    extends Mage_Core_Helper_Abstract 
{
    /**
     * Table rates
     * 
     * @var array
     */
    protected $_tablerates;
    /**
     * Get core helper
     * 
     * @return Innoexts_Core_Helper_Data
     */
    public function getCoreHelper()
    {
        return Mage::helper('innoexts_core');
    }
    /**
     * Get address helper
     * 
     * @return Innoexts_Core_Helper_Address
     */
    public function getAddressHelper()
    {
        return $this->getCoreHelper()->getAddressHelper();
    }
    /**
     * Get table rates
     * 
     * @return array
     */
    public function getTablerates()
    {
        if (is_null($this->_tablerates)) {
            $this->_tablerates = array();
            $tablerateCollection = Mage::getResourceModel('shippingtablerate/tablerate_collection');
            foreach ($tablerateCollection as $tablerate) {
                $this->_tablerates[$tablerate->getId()] = $tablerate;
            }
        }
        return $this->_tablerates;
    }
    /**
     * Retrieve table rate by id
     * 
     * @param int $tablerateId
     * 
     * @return Innoexts_ShippingTablerate_Model_Tablerate
     */
    public function getTablerateById($tablerateId)
    {
        $tablerates = $this->getTablerates();
        if (isset($tablerates[$tablerateId])) {
            return $tablerates[$tablerateId];
        } else {
            return null;
        }
    }
    /**
     * Get websites
     *
     * @return array
     */
    public function getWebsites()
    {
        return Mage::app()->getWebsites();
    }
    /**
     * Get default website
     * 
     * @return Mage_Core_Model_Website
     */
    public function getDefaultWebsite()
    {
        $website = null;
        $websites = $this->getWebsites();
        if (count($websites)) {
            $website = array_shift($websites);
        }
        return $website;
    }
    /**
     * Get website
     * 
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        $website = null;
        $websiteId = (int) Mage::app()->getFrontController()->getRequest()->getParam('website', 0);
        if ($websiteId) {
            $website = Mage::app()->getWebsite($websiteId);
        }
        if (!$website) {
            $website = $this->getDefaultWebsite();
        }
        return $website;
    }
    /**
     * Get website identifier
     * 
     * @param $website|null Mage_Core_Model_Website
     * @return mixed
     */
    public function getWebsiteId($website = null)
    {
        if (is_null($website)) {
            $website = $this->getWebsite();
        }
        return ($website) ? $website->getId() : null;
    }
}