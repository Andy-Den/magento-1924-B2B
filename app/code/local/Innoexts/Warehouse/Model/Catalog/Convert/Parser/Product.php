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
 * Product parser
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Catalog_Convert_Parser_Product 
    extends Mage_Catalog_Model_Convert_Parser_Product 
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
     * Get warehouse identifier
     * 
     * @return int
     */
    protected function getWarehouseId()
    {
        $helper = $this->getWarehouseHelper();
        return $this->getVar('warehouse', $helper->getDefaultWarehouseId());
    }
    /**
     * Get stock identifier
     * 
     * @return int
     */
    protected function getStockId()
    {
        $helper = $this->getWarehouseHelper();
        return $helper->getStockIdByWarehouseId($this->getWarehouseId());
    }
    /**
     * Unparse (prepare data) loaded products
     *
     * @return Innoexts_Warehouse_Model_Catalog_Convert_Parser_Product
     */
    public function unparse()
    {
        $entityIds = $this->getData();
        foreach ($entityIds as $i => $entityId) {
            $product = $this->getProductModel()->setStoreId($this->getStoreId())->load($entityId);
            $stockItem = $this->getWarehouseHelper()->getCatalogInventoryHelper()->getStockItem($this->getStockId());
            $stockItem->assignProduct($product);
            $this->setProductTypeInstance($product);
            $position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
            $this->setPosition($position);
            $row = array(
                'store'         => $this->getStore()->getCode(), 
                'websites'      => '', 
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(), $product->getAttributeSetId()), 
                'type'          => $product->getTypeId(), 
                'category_ids'  => join(',', $product->getCategoryIds()), 
            );
            if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) { $row['url'] = $product->getProductUrl(false); }
            }
            foreach ($product->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) { continue; }
                $attribute = $this->getAttribute($field);
                if (!$attribute) { continue; }
                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) $value = join(self::MULTI_DELIMITER, $option);
                    else $value = $option;
                    unset($option);
                } elseif (is_array($value)) { continue; }
                $row[$field] = $value;
            }
            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) { continue; }
                    $row[$field] = $value;
                }
            }
            $batchExport = $this->getBatchExportModel()->setId(null)->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)->setStatus(1)->save();
            $product->reset();
        }
        return $this;
    }
}