<?php

class TM_AjaxSearch_Model_Observer
{
    protected function _sortProductCollectionByMostViewed($collection, $sort)
    {
        /**
         * Getting event type id for catalog_product_view event
         */
        foreach (Mage::getModel('reports/event_type')->getCollection() as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = (int)$eventType->getId();
                break;
            }
        }

        $select = $collection->getSelect();
        $select
            ->join(array(
                'report_table_views' => $collection->getTable('reports/event')),
                "e.entity_id = report_table_views.object_id",
                array('views' => 'COUNT(report_table_views.event_id)')
            )
            ->where('report_table_views.event_type_id = ?', $productViewEvent)
            ->group('e.entity_id')
            ->order('views ' . $sort)
        ;
//        echo((string)$select);
        return $collection;
    }
    
    protected function _sortProductCollectionByCategoryProducts($collection, $sort)
    {

        $select = $collection->getSelect();
        $select
            ->join(array(
                'catalog_category_product' => $collection->getTable('catalog/category_product')),
                "e.entity_id = catalog_category_product.product_id",
                array('position')
            )
            ->group('e.entity_id')
            ->order('position ' . $sort)
        ;

        return $collection;
    }

    /**
     *
     * @param string $query
     * @param type $store
     * @param Mage_Catalog_Model_Category $category
     * @return type
     */
    public function getProductCollection($query, $store, Mage_Catalog_Model_Category $category = null)
    {
        if (Mage::getStoreConfig('tm_ajaxsearch/general/use_catalogsearch_collection')
            && class_exists('Mage_CatalogSearch_Model_Resource_Search_Collection')) {
            $collection = Mage::getResourceModel('ajaxsearch/product_collection');
            /* @var $collection TM_AjaxSearch_Model_Mysql4_Product_Collection */
            $collection->addSearchFilter($query);
        } else {
            $collection = Mage::getResourceModel('ajaxsearch/collection')
                ->getProductCollection($query);
            /* @var $collection TM_AjaxSearch_Model_Mysql4_Collection */
        }

        $attributeToSort = Mage::getStoreConfig('tm_ajaxsearch/general/sortby');
        $attributeSortOrder = Mage::getStoreConfig('tm_ajaxsearch/general/sortorder');

        if ('most_viewed' === $attributeToSort) {
            $collection = $this->_sortProductCollectionByMostViewed(
                $collection, $attributeSortOrder
            );
        } elseif (null !== $category && 'category_products' === $attributeToSort) {
            $collection = $this->_sortProductCollectionByCategoryProducts(
                $collection, $attributeSortOrder, $category
            );
        } else {
            $collection->addAttributeToSort($attributeToSort, $attributeSortOrder);
        }

        $collection->addStoreFilter($store)
            ->addUrlRewrite()
//            ->addAttributeToSort($attributeToSort, $attributeSortOrder)
            ->setPageSize(Mage::getStoreConfig('tm_ajaxsearch/general/productstoshow'))
        ;

        if (null !== $category) {
            $collection->addCategoryFilter($category);
        }

        Mage::getSingleton('catalog/product_status')
            ->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInSearchFilterToCollection($collection);

        $collection->load();
        return $collection;
    }
//
//    /**
//     *
//     * @param Varien_Event_Observer $observer
//     * @return void
//     */
//    public function resultOverride(Varien_Event_Observer $observer)
//    {
//        Zend_Debug::dump(__METHOD__);
//        die;
//        if (!Mage::getStoreConfig('ajaxsearch/general/enabled')) {
//            return;
//        }
//        if (!Mage::getStoreConfig('ajax_pro/catalogCategoryView/enabled')) {
//            return;
//        }
//
//        $moduleName = $observer->getBlock()->getRequest()->getModuleName();
//        $supportedModules = array('catalog', 'catalogsearch', 'attributepages');
//        if (!in_array($moduleName, $supportedModules)) {
//            return;
//        }
//
//        //core_block_abstract_to_html_after
//        $block  = $observer->getBlock();
//        $transport = $observer->getTransport();
//
//        $blockName = $block->getNameInLayout();
//        if (empty($blockName)) {
//            return;
//        }
//
//        $allowedBlockNames = array('product_list', 'search_result_list');
//        if (!in_array($blockName, $allowedBlockNames)) {
//            return;
//        }
//
//        $refreshBlock = $block->getLayout()->createBlock('core/template')
//            ->setTemplate('tm/ajaxpro/catalog/category/init.phtml')
//        ;
//        $transport->setHtml(
//            $transport->getHtml() .
//            $refreshBlock->toHtml()
//        );
//
//    }
}