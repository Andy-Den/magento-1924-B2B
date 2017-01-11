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
 * Product attributes tab
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes 
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes 
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
     * Retrieve registered product model
     * 
     * @return Mage_Catalog_Model_Product
     */
    protected function getProduct()
    {
        return Mage::registry('product');
    }
    /**
     * Prepare form before rendering HTML
     * 
     * @return self
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $group              = $this->getGroup();
        if (!$group) {
            return $this;
        }
        $form               = $this->getForm();
        $fieldset           = $form->getElement('group_fields'.$group->getId());
        if (!$fieldset) {
            return $this;
        }
        $helper             = $this->getWarehouseHelper();
        $product            = $this->getProduct();
        $blockTypePrefix    = 'warehouse/adminhtml_catalog_product_edit_tab';
        if ($form->getElement('price')) {
            $fieldset->addField(
                'batch_prices', 
                'text', 
                array(
                    'name'      => 'batch_prices', 
                    'label'     => $helper->__('Batch Price'), 
                    'title'     => $helper->__('Batch Price'), 
                    'required'  => false, 
                    'value'     => $product->getBatchPrices(), 
                ), 
                'price'
            )->setRenderer(
                $this->getLayout()->createBlock($blockTypePrefix.'_batchprice_renderer')
            );
        }
        if ($form->getElement('special_price')) {
            $fieldset->addField(
                'batch_special_prices', 
                'text', 
                array(
                    'name'      => 'batch_special_prices', 
                    'label'     => $helper->__('Batch Special Price'), 
                    'title'     => $helper->__('Batch Special Price'), 
                    'required'  => false, 
                    'value'     => $product->getBatchSpecialPrices(), 
                ), 
                'special_price'
            )->setRenderer(
                $this->getLayout()->createBlock($blockTypePrefix.'_batchspecialprice_renderer')
            );
        }
        if ($form->getElement('tax_class_id')) {
            $fieldset->addField(
                'stock_tax_class_ids', 
                'text', 
                array(
                    'name'      => 'stock_tax_class_ids', 
                    'label'     => $helper->__('Warehouse Tax Class'), 
                    'title'     => $helper->__('Warehouse Tax Class'), 
                    'required'  => false, 
                    'value'     => $product->getStockTaxClassIds(), 
                ), 
                'tax_class_id'
            )->setRenderer(
                $this->getLayout()->createBlock($blockTypePrefix.'_stock_tax_class_renderer')
            );
        }
        return $this;
    }
}