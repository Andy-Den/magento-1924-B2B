<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Model_Observer
{
    public function blockPrepareLayoutAfter($observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Permissions_Editroles)
            Mage::helper('amrolepermissions/block')->addRulesTabs($block);
        else if ($block instanceof Mage_Adminhtml_Block_Permissions_Buttons)
            Mage::helper('amrolepermissions/block')->addDuplicateRoleButton($block);
    }

    public function blockCreateAfter($observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Websites)
            Mage::helper('amrolepermissions/block')->alterWebsitesBlock($block);
        else if ($block instanceof Mage_Adminhtml_Block_Store_Switcher)
            Mage::helper('amrolepermissions/block')->alterStoreSwitcher($block);
    }

    public function blockToHtmlAfter($observer)
    {
        $block = $observer->getBlock();

        $transport = $observer->getTransport();
        $html = $transport->getHtml();

        if ($block instanceof Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element)
        {
            if (
                $block->getElement() instanceof Varien_Data_Form_Element_Multiselect ||
                $block->getElement() instanceof Varien_Data_Form_Element_Select
            )
            {
                $htmlId = $block->getElement()->getData('html_id');

                if (in_array($htmlId, array('store_id', 'store_ids', 'sendemail_store_id')))
                    $html = Mage::helper('amrolepermissions/block')->disableStores($html);
                else if (in_array($htmlId, array('website_id', 'website_ids')))
                    $html = Mage::helper('amrolepermissions/block')->disableWebsites($html);
            }
        }
        else if ($block instanceof Mage_Adminhtml_Block_Store_Switcher)
            $html = Mage::helper('amrolepermissions/block')->removeDefaultStoreOption($html);

        $transport->setHtml($html);
    }

    public function permissionsRoleSaverole($observer)
    {
        if (!Mage::registry('current_role')->getId()) // Role wasn't saved
            return;

        $request = $observer->getControllerAction()->getRequest();

        $rule = Mage::getModel('amrolepermissions/rule')->load(
            Mage::registry('current_role')->getId(),
            'role_id'
        );

        $data = $request->getParam('amrolepermissions');

        $rule->update($data, $request->getParam('category_ids'));
    }

    public function actionPreDispatchAdmin($observer)
    {
        /** @var Mage_Adminhtml_Controller_Action $action */
        $action = $observer->getControllerAction();
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = clone $action->getRequest();
        $request->setParamSources();

        if ($request->getControllerName() == 'cms_wysiwyg_images')
            return;

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if (!$rule->getScopeStoreviews())
            return;

        if ($storeId = $request->getParam('store'))
        {
            if (is_array($storeId))
            {
                $storeId = $storeId['store_id'];
            }

            $store = Mage::app()->getStore($storeId);

            if (!$rule->storeAccessible($store->getId()))
            {
                Mage::helper('amrolepermissions')->redirectHome();
            }
        }
        else if ($websiteId = $request->getParam('website'))
        {
            if (is_array($websiteId))
            {
                $websiteId = $websiteId['website_id'];
            }

            $website = Mage::app()->getWebsite($websiteId);

            if (!$rule->hasScopeWebsites() || !in_array($website->getId(), $rule->getScopeWebsites()))
                Mage::helper('amrolepermissions')->redirectHome();
        }
        else if ($group = $request->getParam('group'))
        {
            if (is_array($group))
            {
                $websiteId = $group['website_id'];

                $website = Mage::app()->getWebsite($websiteId);

                if (!$rule->hasScopeWebsites() || !in_array($website->getId(), $rule->getScopeWebsites()))
                    Mage::helper('amrolepermissions')->redirectHome();
            }
        }
        else // Default scope
        {
            if ($request->getControllerName() == 'system_config')
            {
                if ($rule->hasScopeWebsites())
                {
                    $websites = $rule->getScopeWebsites();

                    $website = Mage::app()->getWebsite($websites[0]);

                    Mage::helper('amrolepermissions')->reloadPage(
                        $request,
                        array('website' => $website->getCode())
                    );
                }
                else if ($rule->hasScopeStoreviews())
                {
                    $stores = $rule->getScopeStoreviews();

                    $store = Mage::app()->getStore($stores[0]);

                    Mage::helper('amrolepermissions')->reloadPage(
                        $request,
                        array('store' => $store->getCode())
                    );
                }
            }
            else if ($request->getControllerName() == 'tag') // getAnyStoreView() fix
            {
                $stores = $rule->getScopeStoreviews();

                Mage::helper('amrolepermissions')->reloadPage(
                    $request,
                    array('store' => $stores[0])
                );
            }

        }
        if (Mage::app()->getResponse()->getHttpResponseCode() == '302')
        {
            Mage::app()->getResponse()->sendResponse();
            exit;
        }
    }

    public function modelSaveBefore($observer)
    {
        if (Mage::app()->getRequest()->getModuleName() == 'api')
            return;

        $object = $observer->getObject();

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getScopeStoreviews())
        {
            if ($object->getId())
                Mage::helper('amrolepermissions')->restrictObjectByStores($object->getOrigData());

            Mage::helper('amrolepermissions')->alterObjectStores($object);
        }
    }

    public function modelLoadAfter($observer)
    {
        if (Mage::app()->getRequest()->getModuleName() == 'api')
            return;

        $object = $observer->getObject();

        if (substr(get_class($object), -6) == '_Store')
            return;

        if ($object instanceof Amasty_Rolepermissions_Model_Rule)
            return;

        if (in_array(Mage::app()->getRequest()->getActionName(), array('edit', 'view')) && $object->getId())
        {
            $hlp = Mage::helper('amrolepermissions');

            if ($hlp->canSkipObjectRestriction())
                return;
            if ($hlp->getSkipRestrition())
                return;

            $controllerName = Mage::app()->getRequest()->getControllerName();
            if (substr($controllerName, 0, 6) == 'sales_' && !($object instanceof Mage_Sales_Model_Abstract))
                return;

            if ($controllerName == 'catalog_product' && !($object instanceof Mage_Catalog_Model_Product))
                return;

            if ($controllerName == 'customer')
            {
                if (!($object instanceof Mage_Customer_Model_Customer))
                    return;
                else if ($object->getId() != Mage::app()->getRequest()->getParam('id'))
                    return;
            }
            else if ($controllerName == 'catalog_product') {
                if (!($object instanceof Mage_Catalog_Model_Product))
                    return;
            }

            if ($controllerName == 'cms_page') {
                if (!($object instanceof Mage_Cms_Model_Page)) {
                    return;
                }
            }

            $rule = $hlp->currentRule();

            if ($rule->getScopeStoreviews())
            {
                Mage::helper('amrolepermissions')->restrictObjectByStores($object->getData());
            }
        }
    }

    public function collectionLoadBefore($observer)
    {
        $collection = $observer->getCollection();

        $hlp = Mage::helper('amrolepermissions');

        if ($hlp->checkClass($collection, 'Mage_Core_Model_Mysql4_Store_Collection'))
            return;

        $external = array(
            'Mage_Cms_Model_Mysql4_Page_Collection' => 'cms/page_store',
            'Mage_Cms_Model_Mysql4_Block_Collection' => 'cms/block_store',
            'Mage_Poll_Model_Mysql4_Poll_Collection' => 'poll/poll_store',
            'Mage_Rating_Model_Mysql4_Rating_Collection' => 'rating/rating_store',
            'Mage_Review_Model_Mysql4_Review_Collection' => 'review/review_store',
            'Mage_Review_Model_Mysql4_Review_Product_Collection' => 'review/review_store',
            'Mage_Checkout_Model_Mysql4_Agreement_Collection' => 'checkout/agreement_store',
        );

        $externalWebsite = array(
        );

        $internal = array(
            'Mage_Widget_Model_Mysql4_Widget_Instance_Collection' => 'store_ids',
            'Mage_CatalogSearch_Model_Mysql4_Query_Collection' => 'store_id',
            'AW_Advancednewsletter_Model_Mysql4_Subscriber_Collection' => 'main_table.store_id'
        );

        $additional = array(
            'Mage_Tag_Model_Resource_Tag_Collection' => 'store_id',
        );

        $internalWebsite = array(
        );

        if (Mage::getConfig()->getNode('global/models/catalogrule_resource/entities/website'))
        {
            $externalWebsite += array(
                'Mage_CatalogRule_Model_Mysql4_Rule_Collection' => 'catalogrule/website',
                'Mage_SalesRule_Model_Mysql4_Rule_Collection' => 'salesrule/website',
            );
        }
        else
        {
            $internalWebsite += array(
                'Mage_CatalogRule_Model_Mysql4_Rule_Collection' => 'website_ids',
                'Mage_SalesRule_Model_Mysql4_Rule_Collection' => 'website_ids',
            );
        }

        $rule = $hlp->currentRule();

        if (Mage::helper('core')->isModuleEnabled('Amasty_Xlanding')
            && $hlp->checkClass($collection, 'Amasty_Xlanding_Model_Mysql4_Page_Collection')) {
            $stores = $rule->getScopeStoreviews();
            if (!is_null($stores)) {
                $collection->addStoreFilter($stores, false);
            }
            return;
        }

        if ($rule->getLimitOrders())
        {
            $internal['Mage_Sales_Model_Mysql4_Order_Grid_Collection'] = 'main_table.store_id';
            $internal['Mage_Sales_Model_Mysql4_Order_Collection'] = 'main_table.store_id';
        }

        if ($rule->getLimitInvoices())
        {
            $internal['Mage_Sales_Model_Mysql4_Order_Invoice_Grid_Collection'] = 'main_table.store_id';
            if ($hlp->checkClass($collection, 'Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection'))
            {
                $collection->getSelect()->join(
                    array('am_rp_order' => $collection->getTable('sales/order')),
                    'am_rp_order.entity_id = main_table.order_id',
                    array()
                );

                $internal['Mage_Sales_Model_Mysql4_Order_Payment_Transaction_Collection'] = 'am_rp_order.store_id';
            }
        }

        if ($rule->getLimitShipments())
            $internal['Mage_Sales_Model_Mysql4_Order_Shipment_Grid_Collection'] = 'main_table.store_id';

        if ($rule->getLimitMemos())
            $internal['Mage_Sales_Model_Mysql4_Order_Creditmemo_Grid_Collection'] = 'main_table.store_id';

        if ($rule->getScopeStoreviews())
        {
            if ($hlp->checkClass($collection, 'Mage_Customer_Model_Mysql4_Customer_Collection')
                || $collection instanceof Mage_Customer_Model_Entity_Customer_Collection)
            {
                $collection->addAttributeToFilter('website_id', array('in' => $rule->getPartiallyAccessibleWebsites()));

                return;
            }

            foreach ($externalWebsite as $class => $table)
            {
                if ($hlp->checkClass($collection, $class))
                {
                    $id = $collection->getResource()->getIdFieldName();
                    $websites = $rule->getPartiallyAccessibleWebsites();

                    $select = $collection->getSelect();

                    $select
                        ->join(
                            array('amrolepermissions_website' => $collection->getResource()->getTable($table)),
                            "main_table.$id = amrolepermissions_website.$id",
                            array()
                        )
                        ->where('amrolepermissions_website.website_id IN (?)', $websites)
                        ->distinct()
                    ;

                    return;
                }
            }

            foreach ($external as $class => $table)
            {
                if ($hlp->checkClass($collection, $class))
                {
                    $id = $collection->getResource()->getIdFieldName();
                    $stores = $rule->getScopeStoreviews();
                    // $stores []= 0;

                    $select = $collection->getSelect();

                    $mainAlias = 'main_table';

                    if ($hlp->checkClass($collection, 'Mage_Review_Model_Mysql4_Review_Product_Collection'))
                    {
                        $mainAlias = 'rt';
                        $id = 'review_id';
                    }

                    $select
                        ->join(
                            array('amrolepermissions_store' => $collection->getResource()->getTable($table)),
                            "$mainAlias.$id = amrolepermissions_store.$id",
                            array()
                        )
                        ->where('amrolepermissions_store.store_id IN (?)', $stores)
                        ->distinct()
                    ;

                    return;
                }
            }

            foreach ($internal as $class => $field)
            {
                if ($hlp->checkClass($collection, $class))
                {
                    $stores = $rule->getScopeStoreviews();

                    $select = $collection->getSelect();

                    // sets intersection
                    $storesSql = "";
                    foreach ($stores as $store)
                        $storesSql .= " OR $store IN ($field)";

                    $select->where("0 IN ($field) $storesSql");

                    return;
                }
            }

            foreach ($additional as $class => $field) {
                if ($hlp->checkClass($collection, $class))
                {
                    $stores = $rule->getScopeStoreviews();

                    $select = $collection->getSelect();

                    // sets intersection
                    $storesSql = "";
                    foreach ($stores as $store)
                        $storesSql .= " OR $store IN ($field)";

                    $select->where("$field IS NULL $storesSql");

                    return;
                }
            }

            foreach ($internalWebsite as $class => $field)
            {
                if ($hlp->checkClass($collection, $class))
                {
                    $websites = $rule->getPartiallyAccessibleWebsites();

                    $select = $collection->getSelect();

                    // sets intersection
                    $websitesSql = "";
                    foreach ($websites as $website)
                        $websitesSql .= " OR $website IN ($field)";

                    $select->where("0 IN ($field) $websitesSql");

                    return;
                }
            }
        }
    }

    public function restrictCategoryEdit($observer)
    {
        if ($id = $observer->getControllerAction()->getRequest()->getParam('id'))
        {
            $rule = Mage::helper('amrolepermissions')->currentRule();

            if ($rule->getCategories() && !in_array($id, $rule->getCategories()))
                Mage::helper('amrolepermissions')->redirectHome();

            if (Mage::app()->getRequest()->getActionName() == 'delete')
            {
                if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/delete_categories'))
                    Mage::helper('amrolepermissions')->redirectHome();
            }
        }
    }

    public function categoryPrepareSave($observer)
    {
        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getProducts() || $rule->getScopeStoreviews())
        {
            $category = $observer->getCategory();

            $new = $category->getPostedProducts();

            $old = array();
            foreach ($category->getProductCollection() as $id => $product)
            {
                $old[$id] = $product->getCatIndexPosition();
            }

            $ids = Mage::helper('amrolepermissions')->combine(array_keys($old), array_keys($new), $rule->getAllowedProductIds());

            $priorities = $new + $old;

            foreach ($priorities as $k => $v)
            {
                if (!in_array($k, $ids))
                    unset($priorities[$k]);
            }

            $category->setPostedProducts($priorities);
        }
    }

    public function categoryLoadAfter($observer)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/delete_categories'))
            $observer->getCategory()->setIsDeleteable(false);
    }

    public function categorySaveAfter($observer)
    {
        $categoryId = (int) Mage::app()->getRequest()->getParam('id',false);
        if (!$categoryId) // New category
        {
            $category = $observer->getCategory();

            $this->_updateSubcategoryPermissions($category);
        }
    }

    protected function _updateSubcategoryPermissions($category)
    {
        $rules = Mage::getResourceModel('amrolepermissions/rule_collection')
            ->addFieldToFilter('categories', array('finset' => $category->getParentId()));

        foreach ($rules as $rule)
        {
            $categories = explode(',', $rule->getCategories());
            $categories[] = $category->getId();

            $rule
                ->setCategories(implode(',', $categories))
                ->save();
        }
    }

    public function customerLoadAfter($observer)
    {
        $customer = $observer->getCustomer();

        if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/delete_customers'))
            $customer->setIsDeleteable(false);

        if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/save_customers'))
            $customer->setIsReadonly(true);
    }

    public function prepareMassaction($observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid)
        {
            if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/delete_products'))
                $block->getMassactionBlock()->removeItem('delete');
        }
        else if ($block instanceof Mage_Adminhtml_Block_Customer_Grid)
        {
            if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/delete_customers'))
                $block->getMassactionBlock()->removeItem('delete');

            if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/save_customers'))
            {
                $block->getMassactionBlock()
                    ->removeItem('newsletter_subscribe')
                    ->removeItem('newsletter_unsubscribe')
                    ->removeItem('assign_group')
                ;
            }
        }
    }

    public function canAddSubCategory($observer)
    {
        $allow = Mage::getSingleton('admin/session')
            ->isAllowed('admin/system/amrolepermissions/create_categories');

        $category = $observer->getCategory();

        if ($category && !$category->getId()) {
            $category->setIsReadonly(!$allow);
        }

        $observer->getOptions()->setIsAllow($allow);
    }
}
