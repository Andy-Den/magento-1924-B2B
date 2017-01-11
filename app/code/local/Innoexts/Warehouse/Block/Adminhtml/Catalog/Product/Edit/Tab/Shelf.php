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
 * Product shelf tab
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Shelf 
    extends Mage_Adminhtml_Block_Widget_Form 
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
     * @return Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Shelf
     */
    protected function _prepareForm()
    {
        $product    = $this->getProduct();
        $helper     = $this->getWarehouseHelper();
        $form       = new Varien_Data_Form();
        $form->setHtmlIdPrefix('product_');
        $form->setFieldNameSuffix('product');
        $fieldset   = $form->addFieldset('shelves_info', array('legend' => $helper->__('Shelves'), ));
        $shelvesElement = $fieldset->addField('shelves', 'text', array(
            'name' => 'shelves', 
            'label' => $helper->__('Shelves'), 
            'title' => $helper->__('Shelves'), 
            'required' => false, 
            'value' => $product->getShelves(), 
        ));
        $shelvesElement->setRenderer(
            $this->getLayout()->createBlock('warehouse/adminhtml_catalog_product_edit_tab_shelf_renderer')
        );
        $this->setForm($form);
        return parent::_prepareForm();
    }
}