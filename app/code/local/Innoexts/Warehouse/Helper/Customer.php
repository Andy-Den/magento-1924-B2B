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
 * Customer helper
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Helper_Customer 
    extends Mage_Core_Helper_Abstract 
{
    /**
     * Customer groups
     *
     * @var array of Mage_Customer_Model_Group
     */
    protected $_groups;
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    public function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get customer groups
     * 
     * @return array of Mage_Customer_Model_Group
     */
    public function getGroups()
    {
        if (is_null($this->_groups)) {
            $this->_groups = Mage::getModel('customer/group')->getResourceCollection()->load();
        }
        return $this->_groups;
    }
    /**
     * Get session
     * 
     * @return Mage_Customer_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    /**
     * Get customer group id
     * 
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->getSession()->getCustomerGroupId();
    }
}