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
 * Product grid batch prices renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid_Column_Renderer_Batchprices 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text 
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
     * Get currency code
     * 
     * @param array $row
     * 
     * @return string
     */
    protected function _getCurrencyCode($row)
    {
        $code = $this->getColumn()->getCurrencyCode();
        if ($code) {
            return $code;
        }
        $code = $row->getData($this->getColumn()->getCurrency());
        if ($code) {
            return $code;
        }
        return false;
    }
    /**
     * Get rate
     * 
     * @param array $row
     * 
     * @return float
     */
    protected function _getRate($row)
    {
        $rate = $this->getColumn()->getRate();
        if ($rate) {
            return (float) $rate;
        }
        $rate = $row->getData($this->getColumn()->getRateField());
        if ($rate) {
            return (float) $rate;
        }
        return 1;
    }
    /**
     * Render a grid cell as qtys
     * 
     * @param Varien_Object $row
     * 
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $helper     = $this->getWarehouseHelper();
        $value      = $row->getData($this->getColumn()->getIndex());
        if (is_array($value) && count($value)) {
            $currencyCode   = $this->_getCurrencyCode($row);
            $rate           = $this->_getRate($row);
            $output = '<table cellspacing="0" class="batch-prices-table"><col width="100"/><col width="40"/>';
            foreach ($value as $stockId => $price) {
                if ($currencyCode) {
                    $price = sprintf("%f", ((float) $price) * $rate);
                    $price = Mage::app()->getLocale()->currency($currencyCode)->toCurrency($price);
                }
                $output .= '<tr><td>'.$helper->getWarehouseTitleByStockId($stockId).'</td><td>'.$price.'</td></tr>';
            }
            $output .= '</table>';
            return $output;
        }
        return '';
    }
}