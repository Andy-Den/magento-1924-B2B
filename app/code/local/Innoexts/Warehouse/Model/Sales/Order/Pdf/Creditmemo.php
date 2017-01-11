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
 * Creditmemo PDF model
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Sales_Order_Pdf_Creditmemo 
    extends Mage_Sales_Model_Order_Pdf_Creditmemo 
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
     * Draw table header for product items
     *
     * @param  Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        $helper = $this->getWarehouseHelper();
        
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 30);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 35,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('SKU'), 12, true, true),
            'feed'  => 215,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Total (ex)'), 12, true, true),
            'feed'  => 280,
            'align' => 'right',
            //'width' => 50,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Discount'), 12, true, true),
            'feed'  => 320,
            'align' => 'right',
            //'width' => 50,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Qty'), 12, true, true),
            'feed'  => 375,
            'align' => 'right',
            //'width' => 30,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Tax'), 12, true, true),
            'feed'  => 415,
            'align' => 'right',
            //'width' => 45,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split(Mage::helper('sales')->__('Total (inc)'), 12, true, true),
            'feed'  => 475,
            'align' => 'right'
        );
        
        
        $lines[0][] = array(
            'text'  => $helper->__('Warehouse'), 
            'feed'  => 545,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
    
}