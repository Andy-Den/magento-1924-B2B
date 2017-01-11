<?php
/**
 * FVets_TablePrice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Adminhtml observer
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Model_Adminhtml_Observer
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
     * add the tableprice tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_TablePrice_Model_Adminhtml_Observer
     * @author Douglas Ianitsky
     */
    public function addCategoryTablepriceBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'fvets_tableprice/adminhtml_catalog_category_tab_tableprice',
            'category.tableprice.grid'
        )->toHtml();
        $serializer = $tabs->getLayout()->createBlock(
            'adminhtml/widget_grid_serializer',
            'category.tableprice.grid.serializer'
        );
        $serializer->initSerializerBlock(
            'category.tableprice.grid',
            'getSelectedTablesprices',
            'tablesprices',
            'category_tablesprices'
        );
        $serializer->addColumnInputName('position');
        $content .= $serializer->toHtml();
        $tabs->addTab(
            'tableprice',
            array(
                'label'   => Mage::helper('fvets_tableprice')->__('Tables Prices'),
                'content' => $content,
            )
        );
        return $this;
    }

    /**
     * save table price - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_TablePrice_Model_Adminhtml_Observer
     * @author Douglas Ianitsky
     */
    public function saveCategoryTablepriceData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('tablesprices', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $category = Mage::registry('category');
            $tablepriceCategory = Mage::getResourceSingleton('fvets_tableprice/category')
                ->saveCategoryRelation($category, $post);
        }
        return $this;
    }
}
