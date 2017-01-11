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
 * Shipping table rate method collection
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Shippingtablerate_Tablerate_Method_Collection 
    extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    /**
     * Constructor
     */
    protected function _construct() {
        $this->_init('shippingtablerate/tablerate_method');
    }
    /**
     * Get options array
     * 
     * @param string $valueField
     * 
     * @return array
     */
    public function toOptionArray($valueField = 'method_id')
    {
        return $this->_toOptionArray($valueField, 'name');
    }
    /**
     * Get options hash array
     * 
     * @param string $valueField
     * 
     * @return array
     */
    public function toOptionHash($valueField = 'method_id')
    {
        return $this->_toOptionHash($valueField, 'name');
    }
}