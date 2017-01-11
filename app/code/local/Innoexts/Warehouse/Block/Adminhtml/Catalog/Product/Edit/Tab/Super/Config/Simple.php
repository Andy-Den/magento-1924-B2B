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
 * Product edit tab super config simple
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Super_Config_Simple 
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Simple 
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
     * Prepare form
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Super_Config_Simple
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form       = $this->getForm();
        $fieldset   = $form->getElement('simple_product');
        if (!$fieldset) {
            return $this;
        }
        $fieldset->removeField('simple_product_inventory_qty');
        $fieldset->removeField('simple_product_inventory_is_in_stock');
        $stockHiddenFields = array(
            'use_config_min_qty', 'use_config_min_sale_qty', 'use_config_max_sale_qty', 
            'use_config_backorders', 'use_config_notify_stock_qty', 'is_qty_decimal', 
        );
        foreach ($stockHiddenFields as $fieldName) {
            $fieldset->removeField('simple_product_inventory_'.$fieldName);
        }
        return $this;
    }
}