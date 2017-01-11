<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Adminhtml observer
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     * @author Douglas Ianitsky
     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the restriction group tab to products
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_CatalogRestrictionGroup_Model_Adminhtml_Observer
     * @author Douglas Ianitsky
     */
    public function addProductCatalogrestrictiongroupBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)) {
            $block->addTab(
                'catalogrestrictiongroups',
                array(
                    'label' => Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Groups'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/catalogrestrictiongroup_catalogrestrictiongroup_catalog_product/catalogrestrictiongroups',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                )
            );
        }
        return $this;
    }

    /**
     * save restriction group - product relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_CatalogRestrictionGroup_Model_Adminhtml_Observer
     * @author Douglas Ianitsky
     */
    public function saveProductCatalogrestrictiongroupData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('catalogrestrictiongroups', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $product = Mage::registry('product');
            $catalogrestrictiongroupProduct = Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_product')
                ->saveProductRelation($product, $post);
        }
        return $this;
    }
    /**
     * add the restriction group tab to customers
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_CatalogRestrictionGroup_Model_Adminhtml_Observer
     * @author Douglas Ianitsky
     */
    public function addCustomerCatalogrestrictiongroupBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs) {
            $block->addTabAfter(
                'catalogrestrictiongroups',
                array(
                    'label' => Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Groups'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/catalogrestrictiongroup_customer/catalogrestrictiongroups',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                ),
                'addresses'
            );
        }
        return $this;
    }

    /**
     * save restriction group - customer relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_CatalogRestrictionGroup_Model_Adminhtml_Observer
     * @author Douglas Ianitsky
     */
    public function saveCustomerCatalogrestrictiongroupData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('catalogrestrictiongroups', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $customer = Mage::registry('current_customer');
            $catalogrestrictiongroupCustomer = Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_customer')
                ->saveCustomerRelation($customer, $post);
        }
        return $this;
    }}
