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
 * @package     Innoexts_Shell
 * @copyright   Copyright (c) 2013 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

require_once rtrim(dirname(__FILE__), '/').'/../../../../Core/Importer.php';

/**
 * Product price importer script
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer 
    extends Innoexts_Shell_Core_Importer 
{
    /**
     * Product
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;
    /**
     * Currency codes
     * 
     * @var array
     */
    protected $_currencyCodes;
    /**
     * Retrieve warehouse helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get product
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function getProduct()
    {
        if (is_null($this->_product)) {
            $this->_product = Mage::getModel('catalog/product');
        }
        return $this->_product;
    }
    /**
     * Get currency codes
     * 
     * @return array
     */
    public function getCurrencyCodes()
    {
        if (is_null($this->_currencyCodes)) {
            $currency = Mage::getModel('directory/currency');
            $currencyCodes = $currency->getConfigAllowCurrencies();
            sort($currencyCodes);
            if (count($currencyCodes)) {
                $this->_currencyCodes = $currencyCodes;
            }
        }
        return $this->_currencyCodes;
    }
    /**
     * Get resource
     * 
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    protected function getResource()
    {
        return $this->getProduct()->getResource();
    }
    /**
     * Get adapter
     * 
     * @return Varien_Db_Adapter_Interface
     */
    protected function getWriteAdapter()
    {
        return $this->getResource()->getWriteConnection();
    }
    /**
     * Get select
     * 
     * @return Varien_Db_Select
     */
    protected function getSelect()
    {
        return $this->getWriteAdapter()->select();
    }
    /**
     * Get batch price table name
     * 
     * @return string
     */
    protected function getBatchPriceTableName()
    {
        return 'catalog/product_batch_price';
    }
    /**
     * Get batch special price table name
     * 
     * @return string
     */
    protected function getBatchSpecialPriceTableName()
    {
        return 'catalog/product_batch_special_price';
    }
    /**
     * Get batch price table 
     * 
     * @return string
     */
    protected function getBatchPriceTable()
    {
        return $this->getResource()->getTable($this->getBatchPriceTableName());
    }
    /**
     * Get batch special price table 
     * 
     * @return string
     */
    protected function getBatchSpecialPriceTable()
    {
        return $this->getResource()->getTable($this->getBatchSpecialPriceTableName());
    }
    /**
     * Get batch pricing conditions
     * 
     * @param array $batchPrice
     * @return string
     */
    protected function getBatchPriceConditions($batchPrice)
    {
        $adapter = $this->getWriteAdapter();
        return implode(' AND ', array(
            "(product_id        = {$adapter->quote($batchPrice['product_id'])})", 
            "(UPPER(currency)   = UPPER({$adapter->quote($batchPrice['currency'])}))", 
            "(stock_id          = {$adapter->quote($batchPrice['stock_id'])})", 
            "(store_id          = {$adapter->quote($batchPrice['store_id'])})", 
        ));
    }
    /**
     * Check if batch price exists
     * 
     * @param array $batchPrice
     * @param string $table
     * @return bool
     */
    protected function _isBatchPriceExists($batchPrice, $table)
    {
        $isExists = false;
        $adapter = $this->getWriteAdapter();
        $select = $adapter->select()
            ->from($table, array('COUNT(*)'))
            ->where($this->getBatchPriceConditions($batchPrice));
        $query = $adapter->query($select);
        $count = (int) $query->fetchColumn();
        if ($count) {
            $isExists = true;
        }
        return $isExists;
    }
    /**
     * Check if batch price exists
     * 
     * @param array $batchPrice
     * @return bool
     */
    protected function isBatchPriceExists($batchPrice)
    {
        return $this->_isBatchPriceExists($batchPrice, $this->getBatchPriceTable());
    }
    /**
     * Check if batch special price exists
     * 
     * @param array $batchPrice
     * @return bool
     */
    protected function isBatchSpecialPriceExists($batchPrice)
    {
        return $this->_isBatchPriceExists($batchPrice, $this->getBatchSpecialPriceTable());
    }
    /**
     * Add batch price
     * 
     * @param array $batchPrice
     * @param string $table
     * @return Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer
     */
    protected function _addBatchPrice($batchPrice, $table)
    {
        $adapter = $this->getWriteAdapter();
        $adapter->insert($table, $batchPrice);
        return $this;
    }
    /**
     * Add batch price
     * 
     * @param array $batchPrice
     * @return Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer
     */
    protected function addBatchPrice($batchPrice)
    {
        return $this->_addBatchPrice($batchPrice, $this->getBatchPriceTable());
    }
    /**
     * Add batch special price
     * 
     * @param array $batchPrice
     * @return Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer
     */
    protected function addBatchSpecialPrice($batchPrice)
    {
        return $this->_addBatchPrice($batchPrice, $this->getBatchSpecialPriceTable());
    }
    /**
     * Update batch price
     * 
     * @param array $batchPrice
     * @param string $table
     * @return Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer
     */
    protected function _updateBatchPrice($batchPrice, $table)
    {
        $adapter = $this->getWriteAdapter();
        $adapter->update($table, $batchPrice, $this->getBatchPriceConditions($batchPrice));
        return $this;
    }
    /**
     * Update batch price
     * 
     * @param array $batchPrice
     * @return Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer
     */
    protected function updateBatchPrice($batchPrice)
    {
        return $this->_updateBatchPrice($batchPrice, $this->getBatchPriceTable());
    }
    /**
     * Update batch special price
     * 
     * @param array $batchPrice
     * @return Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer
     */
    protected function updateBatchSpecialPrice($batchPrice)
    {
        return $this->_updateBatchPrice($batchPrice, $this->getBatchSpecialPriceTable());
    }
    /**
     * Append batch price
     * 
     * @param array $batchPrice
     * @return Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer  
     */
    protected function appendBatchPrice($batchPrice)
    {
        if ($this->isBatchPriceExists($batchPrice)) {
            $this->updateBatchPrice($batchPrice);
        } else {
            $this->addBatchPrice($batchPrice);
        }
        return $this;
    }
    /**
     * Append batch special price
     * 
     * @param array $batchPrice
     * @return Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer  
     */
    protected function appendBatchSpecialPrice($batchPrice)
    {
        if ($this->isBatchSpecialPriceExists($batchPrice)) {
            $this->updateBatchSpecialPrice($batchPrice);
        } else {
            $this->addBatchSpecialPrice($batchPrice);
        }
        return $this;
    }
    /**
     * Reindex
     * 
     * @return Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer
     */
    protected function reindex()
    {
        $this->printMessage('Reindexing.');
        $productPriceProcess = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price');
        if ($productPriceProcess) {
            $productPriceProcess->reindexAll();
        }
        return $this;
    }
    /**
     * Import row
     * 
     * @param array $row
     * @return bool
     */
    protected function importRow($row)
    {
        $helper = $this->getWarehouseHelper();
        $isImported = false;
        $sku = null;
        if (isset($row['sku']) && $row['sku']) {
            $sku = $row['sku'];
        }
        if ($sku) {
            $product = $this->getProduct();
            $productId = $product->getIdBySku($sku);
            if ($productId) {
                $currencies = $this->getCurrencyCodes();
                $stockIds = $helper->getStockIds();
                if (count($currencies) && count($stockIds)) {
                    $storeId = (isset($row['store']) && $row['store']) ? $row['store'] : 0;
                    if ($storeId) {
                        $storeId = Mage::app()->getStore($storeId)->getId();
                    } else {
                        $storeId = 0;
                    }
                    foreach ($currencies as $currency) {
                        $currency = strtolower($currency);
                        foreach ($stockIds as $stockId) {
                            $code = $helper->getWarehouseCodeByStockId($stockId);
                            if ($code) {
                                $price              = null;
                                $priceKey           = 'price_'.$currency.'_'.$code;
                                $priceKey2          = 'price_'.$currency.'_'.$stockId;
                                if (isset($row[$priceKey]) && $row[$priceKey]) {
                                    $price = (float) $row[$priceKey];
                                } else if (isset($row[$priceKey2]) && $row[$priceKey2]) {
                                    $price = (float) $row[$priceKey2];
                                }
                                if (!is_null($price)) {
                                    $batchPrice = array(
                                        'product_id'    => $productId, 
                                        'currency'      => strtoupper($currency), 
                                        'stock_id'      => $stockId, 
                                        'store_id'      => $storeId, 
                                        'price'         => $price, 
                                    );
                                    $this->appendBatchPrice($batchPrice);
                                }
                                
                                $specialPrice       = null;
                                $specialPriceKey    = 'special_price_'.$currency.'_'.$code;
                                $specialPriceKey2   = 'special_price_'.$currency.'_'.$stockId;
                                if (isset($row[$specialPriceKey]) && $row[$specialPriceKey]) {
                                    $specialPrice       = (float) $row[$specialPriceKey];
                                } else if (isset($row[$specialPriceKey2]) && $row[$specialPriceKey2]) {
                                    $specialPrice       = (float) $row[$specialPriceKey2];
                                }
                                if (!is_null($specialPrice)) {
                                    $batchSpecialPrice = array(
                                        'product_id'    => $productId, 
                                        'currency'      => strtoupper($currency), 
                                        'stock_id'      => $stockId, 
                                        'store_id'      => $storeId, 
                                        'price'         => $specialPrice, 
                                    );
                                    $this->appendBatchSpecialPrice($batchSpecialPrice);
                                }
                            }
                        }
                    }
                }
            } else {
                $this->printMessage("Can't find product by sku: {$sku}");
            }
        }
        return $isImported;
    }
}

$shell = new Innoexts_Shell_WarehousePlus_Catalog_Product_Price_Importer();
$shell->run();

/**
php shell/Innoexts/WarehousePlus/Catalog/Product/Price/Importer.php \
    --file-path /var/import/ \
    --file-filename localfilename.csv

php shell/Innoexts/WarehousePlus/Catalog/Product/Price/Importer.php \
    --ftp \
    --ftp-host ftp.yourhost.com \
    --ftp-user username \
    --ftp-password password \
    --ftp-filename remotefilename.csv \
    --file-path /var/import/ \
    --file-filename localfilename.csv
 */
/*
php shell/Innoexts/WarehousePlus/Catalog/Product/Price/Importer.php \
    --file-path /var/import/Innoexts/WarehousePlus/ \
    --file-filename product-prices.csv
*/
