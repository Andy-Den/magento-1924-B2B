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
 * Abstact single warehouse assignment method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Single_Abstract 
    extends Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Abstract 
{
    /**
     * Get stock identifier
     * 
     * @var int
     */
    protected $_stockId;
    /**
     * Set quote
     * 
     * @param Innoexts_Warehouse_Model_Sales_Quote $quote
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract
     */
    public function setQuote($quote)
    {
        if (is_null($this->_quote) && is_null($quote)) {
            return $this;
        }
        $this->_quote = $quote;
        $this->_stockId = null;
        return $this;
    }
    /**
     * Apply quote stock items
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Single_Abstract
     */
    public function applyQuoteStockItems()
    {
        $quote = $this->getQuote();
        if (!$quote) {
            return $this;
        }
        $stockId = $this->getStockId();
        if ($stockId) {
            foreach ($quote->getAllItems() as $item) {
                $item->setStockId($stockId);
            }
        }
        return $this;
    }
    /**
     * Get stock identifier
     * 
     * @return int
     */
    protected function _getStockId()
    {
        return $this->getWarehouseHelper()->getDefaultStockId();
    }
    /**
     * Get stock identifier
     * 
     * @return int
     */
    public function getStockId()
    {
        if (is_null($this->_stockId)) {
            $stockId = null;
            $helper     = $this->getWarehouseHelper();
            $config     = $helper->getConfig();
            if ($config->isAllowAdjustment()) {
                $stockId    = $helper->getSessionStockId();
            }
            if (!$stockId) {
                $quote = $this->getQuote();
                if ($quote) {
                    $stockId = $quote->getStockId();
                }
                if (!$stockId) {
                    $stockId = $this->_getStockId();
                }
            }
            $this->_stockId = $stockId;
        }
        return $this->_stockId;
    }
}