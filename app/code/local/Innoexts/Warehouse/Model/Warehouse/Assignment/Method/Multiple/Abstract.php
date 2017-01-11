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
 * Abstact multiple warehouse assignment method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract 
    extends Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Abstract 
{
    /**
     * Product stock identifiers
     * 
     * @var array
     */
    protected $_productStockIds;
    /**
     * Get quote value
     * 
     * @return float
     */
    protected function getValueGetter()
    {
        return 'getGrandTotal';
    }
    /**
     * Apply quote stock items
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract
     */
    public function applyQuoteStockItems()
    {
        $quote          = $this->getQuote();
        if (!$quote) {
            return $this;
        }
        $quoteHelper    = $this->getWarehouseHelper()->getQuoteHelper();
        $stockData      = $quoteHelper->getStockData($quote);
        if (!is_null($stockData)) {
            $quoteHelper->applyStockItems($quote, $stockData, $this->getValueGetter());
        }
        return $this;
    }
    /**
     * Get product stock identifier
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return int
     */
    protected function _getProductStockId($product)
    {
        return $this->getWarehouseHelper()->getDefaultStockId();
    }
    /**
     * Get product stock identifier
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return int
     */
    public function getProductStockId($product)
    {
        $productId = (int) $product->getId();
        if (!isset($this->_productStockIds[$productId])) {
            $helper     = $this->getWarehouseHelper();
            $config     = $helper->getConfig();
            $stockId    = null;
            if ($config->isAllowAdjustment()) {
                $productHelper  = $helper->getProductHelper();
                $sessionStockId = $productHelper->getSessionStockId($product);
                if ($sessionStockId) {
                    $stockId = $sessionStockId;
                }
            }
            if (!$stockId) {
                $stockId = $this->_getProductStockId($product);
            }
            $this->_productStockIds[$productId] = $stockId;
        }
        return $this->_productStockIds[$productId];
    }
}