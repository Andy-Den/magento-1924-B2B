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
 * Quote item collection
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Sales_Quote_Item_Collection 
    extends Mage_Sales_Model_Mysql4_Quote_Item_Collection 
{
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * After load processing
     *
     * @return Innoexts_Warehouse_Model_Mysql4_Sales_Quote_Item_Collection
     */
    protected function _afterLoad()
    {
        $dataChanges = array();
        foreach ($this->_items as $item) {
            $productId = $item->getProductId();
            $stockId = $item->getStockId();
            $dataChanges[$productId][$stockId] = $item->getDataChanges();
        }
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            $productId = $item->getProductId();
            $stockId = $item->getStockId();
            if (isset($dataChanges[$productId]) && isset($dataChanges[$productId][$stockId])) {
                $item->setDataChanges($dataChanges[$productId][$stockId]);
            }
        }
        return $this;
    }
    /**
     * Add products to items and item options
     *
     * @return Mage_Sales_Model_Mysql4_Quote_Item_Collection
     */
    protected function _assignProducts()
    {
        $productIds = array();
        foreach ($this as $item) {
            $productIds[] = $item->getProductId();
        }
        $this->_productIds = array_merge($this->_productIds, $productIds);
        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->setStoreId($this->getStoreId())->addIdFilter($this->_productIds)
            ->addAttributeToSelect(Mage::getSingleton('sales/quote_config')->getProductAttributes())
            ->addOptionsToResult()->addStoreFilter()->addUrlRewrite()->addTierPriceData();
        Mage::dispatchEvent('prepare_catalog_product_collection_prices', array(
            'collection' => $productCollection, 'store_id' => $this->getStoreId(), 
        ));
        Mage::dispatchEvent('sales_quote_item_collection_products_after_load', array('product_collection' => $productCollection));
        $recollectQuote = false;
        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                $product->setCustomOptions(array());
                $qtyOptions         = array();
                $optionProductIds   = array();
                foreach ($item->getOptions() as $option) {
                    $product->getTypeInstance(true)->assignProductToOption(
                        $productCollection->getItemById($option->getProductId()), $option, $product
                    );
                    if (is_object($option->getProduct()) && $option->getProduct()->getId() != $product->getId()) {
                        $optionProductIds[$option->getProduct()->getId()] = $option->getProduct()->getId();
                    }
                }
                if ($optionProductIds) {
                    foreach ($optionProductIds as $optionProductId) {
                        $qtyOption = $item->getOptionByCode('product_qty_' . $optionProductId);
                        if ($qtyOption) $qtyOptions[$optionProductId] = $qtyOption;
                    }
                }
                $item->setQtyOptions($qtyOptions);
                $item->setProduct($product);
            } else {
                $item->isDeleted(true);
                $recollectQuote = true;
            }
        }
        foreach ($this as $item) {
            $item->checkData();
        }
        if ($recollectQuote && $this->_quote) {
            $this->_quote->collectTotals();
        }
        return $this;
    }
}