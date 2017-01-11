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
 * Product observer
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Model_Catalog_Product_Observer 
    extends Innoexts_Warehouse_Model_Catalog_Product_Observer 
{
    /**
     * Get request
     * 
     * @return Mage_Core_Controller_Request_Http
     */
    protected function getRequest()
    {
        return Mage::app()->getRequest();
    }
    /**
     * Add zone price tab
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return Innoexts_WarehousePlus_Model_Catalog_Product_Observer
     */
    public function addZonePriceTab(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!$block || !($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs)) {
            return $this;
        }
        $helper     = $this->getWarehouseHelper();
        $request    = $this->getRequest();
        if (($request->getActionName() == 'edit') || ($request->getParam('type'))) {
            $after      = null;
            $tabBlock   = $block->getLayout()->createBlock(
                'warehouseplus/adminhtml_catalog_product_edit_tab_zone_price'
            );
            $product    = $tabBlock->getProduct();
            if ($product && $product->getAttributeSetId()) {
                $attributeSetId     = $product->getAttributeSetId();
                $groupCollection    = Mage::getResourceModel('eav/entity_attribute_group_collection')
                    ->setAttributeSetFilter($attributeSetId);
                foreach ($groupCollection as $group) {
                    if (strtolower($group->getAttributeGroupName()) == 'prices') {
                        $after = 'group_'.$group->getId();
                        break;
                    }
                }
            }
            if (!$after) {
                $tabIds = $block->getTabsIds();
                $after = ((array_search('inventory', $tabIds) !== false) && (array_search('inventory', $tabIds) > 0)) ? 
                    $tabIds[array_search('inventory', $tabIds) - 1] : 'websites';
            }
            $block->addTab('zone_price', array(
                'after'     => $after, 
                'label'     => $helper->__('Zone Discounts'), 
                'content'   => $tabBlock->toHtml(), 
            ));
        }
        return $this;
    }
}