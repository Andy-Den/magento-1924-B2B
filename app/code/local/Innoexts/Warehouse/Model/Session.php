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
 * Warehouse session
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Session 
    extends Mage_Core_Model_Session_Abstract 
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $namespace = 'warehouse';
        $this->init($namespace);
        Mage::dispatchEvent('warehouse_session_init', array('geocoder_session' => $this));
    }
    /**
     * Set product stock ids
     * 
     * @param array $productStockIds
     * 
     * @return Innoexts_Warehouse_Model_Session
     */
    public function setProductStockIds($productStockIds)
    {
        $this->setData('product_stock_ids', $productStockIds);
        return $this;
    }
    /**
     * Get product stock ids
     * 
     * @return array
     */
    public function getProductStockIds()
    {
        $productStockIds = $this->getData('product_stock_ids');
        if (!is_array($productStockIds)) {
            $productStockIds = array();
        }
        return $productStockIds;
    }
    /**
     * Get product stock ids hash
     * 
     * @return string
     */
    public function getProductStockIdsHash()
    {
        return md5(serialize($this->getProductStockIds()));
    }
    /**
     * Remove product stock ids
     * 
     * @return Innoexts_Warehouse_Model_Session
     */
    public function removeProductStockIds()
    {
        $this->unsetData('product_stock_ids');
        return $this;
    }
    /**
     * Set product stock id
     * 
     * @param int $productId
     * @param int $stockId
     * 
     * @return Innoexts_Warehouse_Model_Session
     */
    public function setProductStockId($productId, $stockId)
    {
        $productStockIds = $this->getProductStockIds();
        $productStockIds[$productId] = $stockId;
        $this->setProductStockIds($productStockIds);
        return $this;
    }
    /**
     * Get product stock id
     * 
     * @param int $productId
     * 
     * @return int
     */
    public function getProductStockId($productId)
    {
        $productStockIds = $this->getProductStockIds();
        if (isset($productStockIds[$productId]) && ($productStockIds[$productId])) {
            return (int) $productStockIds[$productId];
        } else {
            return null;
        }
    }
    /**
     * Set stock id
     * 
     * @param int $stockId
     * 
     * @return Innoexts_Warehouse_Model_Session
     */
    public function setStockId($stockId)
    {
        $this->setData('stock_id', $stockId);
        return $this;
    }
    /**
     * Get stock id
     * 
     * @return int
     */
    public function getStockId()
    {
        $stockId = $this->getData('stock_id');
        return ($stockId) ? (int) $stockId : null;
    }
    /**
     * Remove stock id
     * 
     * @return Innoexts_Warehouse_Model_Session
     */
    public function removeStockId()
    {
        $this->unsetData('stock_id');
        return $this;
    }
}