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
 * Priority multiple warehouse assignment method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Priority 
    extends Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract 
{
    /**
     * Apply quote stock items
     * 
     * @param Innoexts_Warehouse_Model_Sales_Quote $quote
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Priority
     */
    public function applyQuoteStockItems($quote = null)
    {
        if (is_null($quote)) {
            $quote          = $this->getQuote();
        }
        if (!$quote) {
            return $this;
        }
        $quoteHelper    = $this->getWarehouseHelper()->getQuoteHelper();
        $stockData      = $quoteHelper->getStockData($quote);
        if (!is_null($stockData)) {
            if (count($stockData)) {
                $productHelper = $this->getWarehouseHelper()->getProductHelper();
                $combination = array();
                foreach ($stockData as $itemKey => $itemStockData) {
                    $minStockId = null;
                    if ($itemStockData->getIsInStock()) {
                        if (!$itemStockData->getSessionStockId()) {
                            $stockIds = array();
                            foreach ($itemStockData->getStockItems() as $stockItem) {
                                $stockId = (int) $stockItem->getStockId();
                                if ($stockId) {
                                    array_push($stockIds, $stockId);
                                }
                            }
                            $minStockId = $productHelper->getMinPriorityStockId($itemStockData->getProduct(), $stockIds);
                        } else {
                            $minStockId = $itemStockData->getSessionStockId();
                        }
                    }
                    if (is_null($minStockId)) {
                        $combination = null;
                        break;
                    }
                    $combination[$itemKey] = $minStockId;
                }
                if (!is_null($combination)) {
                    $quote->applyStockItemsCombination($stockData, $combination);
                }
            }
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
        $helper         = $this->getWarehouseHelper();
        $productHelper  = $helper->getProductHelper();
        $stockIds       = $productHelper->getQuoteInStockStockIds($product);
        $stockId        = $productHelper->getMinPriorityStockId($product, $stockIds);
        return ($stockId) ? $stockId : $helper->getDefaultStockId();
    }
}