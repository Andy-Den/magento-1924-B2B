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
 * Stock collection
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Cataloginventory_Stock_Collection 
    extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('cataloginventory/stock');
    }
    /**
     * Retrieves ids
     *
     * @return  array
     */
    public function getIds()
    {
        $ids = array();
        foreach ($this as $stock) { 
            array_push($ids, $stock->getId()); 
        }
        return $ids;
    }
    /**
     * Convert to array for select options
     *
     * @param   bool $emptyLabel
     * 
     * @return  array
     */
    public function toOptionArray($emptyLabel = '')
    {
        $options = $this->_toOptionArray('stock_id', 'stock_name', array());
        if (count($options) > 0 && $emptyLabel !== false) {
            array_unshift($options, array('value' => '', 'label' => $emptyLabel));
        }
        return $options;
    }
}