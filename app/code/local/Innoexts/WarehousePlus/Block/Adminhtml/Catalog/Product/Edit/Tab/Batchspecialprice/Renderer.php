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
 * @package     Innoexts_WarehousePlus
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Product batch price renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Block_Adminhtml_Catalog_Product_Edit_Tab_Batchspecialprice_Renderer 
    extends Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Batchspecialprice_Renderer 
{
    /**
     * Constructor
     */
    public function __construct() {
        $this->setTemplate('warehouseplus/catalog/product/edit/tab/batchspecialprice/renderer.phtml');
    }
    /**
     * Check if store price scope is active
     * 
     * @return bool
     */
    public function isStorePriceScope()
    {
        return $this->getWarehouseHelper()->getProductPriceHelper()->isStoreScope();
    }
    /**
     * Get currency codes
     * 
     * @return array
     */
    public function getCurrencyCodes()
    {
        return $this->getWarehouseHelper()->getCurrencyHelper()->getCodes();
    }
    /**
     * Get price scope string
     * 
     * @return string
     */
    public function getPriceScopeStr()
    {
        $scope = null;
        if ($this->isStorePriceScope()) {
            $scope = '[STORE VIEW]';
        } else if ($this->isWebsitePriceScope()) {
            $scope = '[WEBSITE]';
        } else {
            $scope = '[GLOBAL]';
        }
        return $scope;
    }
    /**
     * Sort values function
     *
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    protected function sortValues($a, $b)
    {
        if ($a['stock_id'] != $b['stock_id']) {
            return $a['stock_id'] < $b['stock_id'] ? -1 : 1;
        }
        if ($a['currency'] != $b['currency']) {
            return $a['currency'] < $b['currency'] ? -1 : 1;
        }
        return 0;
    }
    /**
     * Get values
     * 
     * @return array
     */
    public function getValues()
    {
        $helper             = $this->getWarehouseHelper();
        $productHelper      = $helper->getProductHelper();
        $priceHelper        = $helper->getProductPriceHelper();
        $currencies         = $this->getCurrencyCodes();
        $stockIds           = $helper->getStockIds();
        $values             = array();
        if (count($currencies) && count($stockIds)) {
            $element            = $this->getElement();
            $readonly           = $element->getReadonly();
            $store              = $this->getStore();
            $baseCurrency       = $store->getBaseCurrency();
            $product            = $this->getProduct();
            $currentStoreId     = $productHelper->getStoreId($product);
            $storeId            = $store->getId();
            $prices             = $product->getBatchSpecialPrices();
            $data               = (isset($prices[$currentStoreId])) ? $prices[$currentStoreId] : array();
            foreach ($currencies as $currency) {
                if (!$baseCurrency->getRate($currency)) {
                    continue;
                }
                foreach ($stockIds as $stockId) {
                    $value = array(
                        'currency'  => $currency, 
                        'stock_id'  => $stockId, 
                    );
                    $defaultPrice = $priceHelper->getDefaultSpecialPrice2($product, $storeId, $currency, $stockId);
                    $defaultPrice = $priceHelper->getEscapedPrice($defaultPrice);
                    if (isset($data[$currency]) && isset($data[$currency][$stockId])) {
                        $value['price']         = $priceHelper->getEscapedPrice($data[$currency][$stockId]);
                        $value['default_price'] = $defaultPrice;
                        $value['use_default']   = 0;
                    } else {
                        if (!is_null($defaultPrice)) {
                            $value['price']         = $defaultPrice;
                        } else {
                            $value['price']         = null;
                        }
                        $value['default_price'] = $defaultPrice;
                        $value['use_default']   = 1;
                    }
                    $value['readonly'] = $readonly;
                    $value['rate'] = array();
                    foreach ($currencies as $_currency) {
                        $value['rate'][$_currency] = $helper->getCurrencyHelper()->getRate($_currency, $currency);
                    }
                    array_push($values, $value);
                }
            }
        }
        usort($values, array($this, 'sortValues'));
        return $values;
    }
}