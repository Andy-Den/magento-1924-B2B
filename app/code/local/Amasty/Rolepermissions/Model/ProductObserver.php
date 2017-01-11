<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Model_ProductObserver
{
    public function productSaveBefore($observer)
    {
        if (Mage::app()->getRequest()->getModuleName() == 'api')
            return;

        $product = $observer->getProduct();
        $hlp = Mage::helper('amrolepermissions');
        $rule = $hlp->currentRule();

        if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/save_products'))
            $hlp->redirectHome();

        if (!$rule->checkProductPermissions($product)
            && !$rule->checkProductOwner($product)
        ) {
            $hlp->redirectHome();
        }

        if ($rule->getScopeStoreviews())
        {
            $orig = Mage::getResourceModel('catalog/product')->getWebsiteIds($product);
            $new = $product->getData('website_ids');

            if ($orig != $new)
            {
                $ids = Mage::helper('amrolepermissions')->combine($orig, $new, $rule->getPartiallyAccessibleWebsites());
                $product->setWebsiteIds($ids);
            }

            if (!$product->getId())
            {
                $product->setData('amrolepermissions_disable');
                //$product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
            }
        }

        if ($rule->getCategories())
        {
            $originCategoryIds = $product->getResource()->getCategoryIds($product);
            $ids = Mage::helper('amrolepermissions')->combine(
                $originCategoryIds,
                $product->getCategoryIds(),
                $rule->getCategories()
            );
            $product->setCategoryIds($ids);
        }

        if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/product_owner')
            && Mage::getSingleton('admin/session')->getUser()){
            $product->unsetData('amrolepermissions_owner');
        }
    }

    public function productSaveAfter($observer)
    {
        if (Mage::app()->getRequest()->getModuleName() == 'api')
            return;

        $product = $observer->getProduct();

        if (!$product->getOrigData('entity_id'))
        {
            if (!$product->getAmrolepermissionsOwner())
            {
                $user = Mage::getSingleton('admin/session')->getUser();

                if ($user)
                {
                    $product->setAmrolepermissionsOwner($user->getId());
                    $product->getResource()->saveAttribute($product, 'amrolepermissions_owner');
                }
            }
        }

        if ($product->hasData('amrolepermissions_disable'))
        {
            $rule = Mage::helper('amrolepermissions')->currentRule();

            $resource = $product->getResource();

            $status = $product->getStatus();

            $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
            $resource->saveAttribute($product, 'status');

            $resource->getAttribute('status')->setIsGlobal(Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);

            $preservedStoreId = $product->getStoreId(); // Just in case

            $product->setStatus($status);
            foreach ($rule->getScopeStoreviews() as $storeId)
            {
                $product->setStoreId($storeId);
                $resource->saveAttribute($product, 'status');
            }

            $product->setStoreId($preservedStoreId);
        }
    }

    public function productLoadAfter($observer)
    {
        if (Mage::app()->getRequest()->getModuleName() == 'api')
            return;

        $hlp = Mage::helper('amrolepermissions');

        $controller = Mage::app()->getRequest()->getControllerName();
        if (substr($controller, 0, 6) == 'sales_')
            return;

        $rule = $hlp->currentRule();

        $product = $observer->getProduct();

        if ($product->getData('_edit_mode')
            && !$rule->checkProductOwner($product)
        ) { // Indexer fix
            if (!$rule->checkProductPermissions($product))
                $hlp->redirectHome();

            if ($rule->getCategories())
            {
                $productCategories = $product->getCategoryIds();
                if (!array_intersect($productCategories, $rule->getCategories()))
                    $hlp->redirectHome();
            }
        }

        if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/delete_products'))
            $product->setIsDeleteable(false);
    }

    public function layoutRenderBeforeProductEdit($observer)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/save_products'))
        {
            Mage::app()->getLayout()->getBlock('product_edit')
                ->unsetChild('save_button')
                ->unsetChild('reset_button')
                ->unsetChild('save_and_edit_button')
                ->unsetChild('duplicate_button')
            ;

            Mage::registry('current_product')->setIsReadonly(true);
        }
    }

    public function layoutRenderBeforeProductIndex($observer)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/save_products'))
        {
            Mage::app()->getLayout()->getBlock('products_list')
                ->removeButton('add_new')
            ;
        }
    }

    public function productCollectionLoadBefore($observer)
    {
        if (Mage::app()->getRequest()->getModuleName() == 'api')
            return;

        $rule = Mage::helper('amrolepermissions')->currentRule();

        $rule->restrictProductCollection($observer->getCollection());
    }

    public function removeOwnerField($observer)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('admin/system/amrolepermissions/product_owner'))
        {
            foreach ($observer->getForm()->getElements() as $element)
                $element->removeField('amrolepermissions_owner');
        }
    }

    public function restrictProductDelete($observer)
    {
        $request = $observer->getControllerAction()->getRequest();

        $ids = $request->getParam('product');

        if (!is_array($ids))
        {
            $id = $request->getParam('id');
            if ($id)
                $ids = array($ids);
        }

        if (!$ids)
            Mage::helper('amrolepermissions')->redirectHome();

        $rule = Mage::helper('amrolepermissions')->currentRule();
        $allowed = $rule->getAllowedProductIds();

        if (array_diff(array_intersect($allowed, $ids), $ids) !== array())
            Mage::helper('amrolepermissions')->redirectHome();
    }

}
