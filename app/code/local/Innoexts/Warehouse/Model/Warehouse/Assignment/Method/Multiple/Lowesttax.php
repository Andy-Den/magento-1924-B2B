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
 * Lowest tax multiple warehouse assignment method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Lowesttax 
    extends Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract 
{
    /**
     * Get quote value
     * 
     * @return float
     */
    protected function getValueGetter()
    {
        return 'getTaxAmount';
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
        $stockId        = $productHelper->getQuoteMinTaxAmountStockId($product, $stockIds);
        return ($stockId) ? $stockId : $helper->getDefaultStockId();
    }
}