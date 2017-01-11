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
 * Configurable product view
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Catalog_Product_View_Type_Configurable 
    extends Mage_Catalog_Block_Product_View_Type_Configurable 
{
    /**
     * Get warehouse helper
     *
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    protected function getVersionHelper()
    {
        return $this->getWarehouseHelper()->getVersionHelper();
    }
    /**
     * Get allowed products
     * 
     * @return array of Mage_Catalog_Model_Product
     */
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $helper                 = $this->getWarehouseHelper();
            $config                 = $helper->getConfig();
            $assignmentMethodHelper = $helper->getAssignmentMethodHelper();
            $inventoryHelper        = $helper->getCatalogInventoryHelper();
            $products               = array();
            $parentProduct          = $this->getProduct();
            $parentStockItem        = $parentProduct->getStockItem();
            $parentProductId        = $parentProduct->getId();
            
            if ($this->getVersionHelper()->isGe1700()) {
                $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
            }
            
            $allProducts = $parentProduct->getTypeInstance(true)->getUsedProducts(null, $parentProduct);
            if ($config->isMultipleMode()) {
                $stockIds = $inventoryHelper->getStockIds();
            } else {
                $stockIds = array($assignmentMethodHelper->getQuoteStockId());
            }
            foreach ($allProducts as $product) {
                $productId = $product->getId();
                foreach ($stockIds as $stockId) {
                    $pStockItem = $inventoryHelper->getStockItemCached($parentProductId, $stockId);
                    $pStockItem->assignProduct($parentProduct);
                    $stockItem = $inventoryHelper->getStockItemCached($productId, $stockId);
                    $stockItem->assignProduct($product);
                    if ($this->getVersionHelper()->isGe1700()) {
                        if (($product->isSaleable() && $parentProduct->isSaleable()) || $skipSaleableCheck) {
                            $products[] = $product;
                            break;
                        }
                    } else {
                        if ($product->isSaleable() && $parentProduct->isSaleable()) {
                            $products[] = $product;
                            break;
                        }
                    }
                }
            }
            $parentStockItem->assignProduct($parentProduct);
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }
}