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
 * Zone controller
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Adminhtml_Catalog_Product_ZoneController 
    extends Innoexts_Core_Controller_Adminhtml_Action 
{
    /**
     * Model names
     * 
     * @var array
     */
    protected $_modelNames = array(
        'product'               => 'catalog/product', 
        'product_zone_price'    => 'catalog/product_zone_price', 
    );
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_WarehousePlus_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Initialize product
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $productId  = (int) $this->getRequest()->getParam('id');
        $product = Mage::getModel('catalog/product')->setStoreId($this->getRequest()->getParam('store', 0));
        if (!$productId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }
            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
        }
        $product->setData('_edit_mode', true);
        if ($productId) {
            try {
                $product->load($productId);
            } catch (Exception $e) {
                $product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
                Mage::logException($e);
            }
        }
        Mage::register('product', $product);
        Mage::register('current_product', $product);
        return $product;
    }
    /**
     * Price grid action
     * 
     * @return Innoexts_WarehousePlus_Adminhtml_Catalog_Product_ZoneController
     */
    public function priceGridAction()
    {
        $this->_initProduct();
        $this->_gridAction('product_zone_price', true);
    }
    /**
     * Prepare save
     * 
     * @param string $type
     * @param Mage_Core_Model_Abstract $model
     * 
     * @return Innoexts_WarehousePlus_Adminhtml_Catalog_Product_ZoneController
     */
    protected function _prepareSave($type, $model)
    {
        if ($type == 'product_zone_price') {
            $productId = $this->getRequest()->getParam('id');
            $model->setProductId($productId);
        }
        return $this;
    }
    /**
     * Edit price action
     * 
     * @return Innoexts_WarehousePlus_Adminhtml_Catalog_Product_ZoneController
     */
    public function editPriceAction()
    {
        $helper = $this->getWarehouseHelper();
        $this->_editAction(
            'product_zone_price', true, null, 'zone_price_id', null, null, null, array(), 
            $helper->__('The price was not found.')
        );
        return $this;
    }
    /**
     * Save price action
     * 
     * @return Innoexts_WarehousePlus_Adminhtml_Catalog_Product_ZoneController
     */
    public function savePriceAction()
    {
        $helper = $this->getWarehouseHelper();
        $this->_saveAction(
            'product_zone_price', true, 'zone_price_id', null, null, 
            $helper->__('The price has been saved.'), 
            $helper->__('An error occurred while saving the price: %s.')
        );
        return $this;
    }
    /**
     * Delete price action
     * 
     * @return Innoexts_WarehousePlus_Adminhtml_Catalog_Product_ZoneController
     */
    public function deletePriceAction()
    {
        $helper = $this->getWarehouseHelper();
        $this->_deleteAction(
            'product_zone_price', true, 'zone_price_id', null, null, 
            $helper->__('The price was not found.'), 
            $helper->__('The price has been deleted.')
        );
        return $this;
    }
}