<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Model_CategoryObserver
{
    public function onPredispatchCatalogCategoryEdit($observer)
    {
        $rule = Mage::helper('amrolepermissions')->currentRule();
        $catRestrictions = $rule->getCategories();

        if (!$catRestrictions)
            return;

        $request = Mage::app()->getRequest();

        $storeId = $request->getParam('store');

        if ($storeId) {
            Mage::getSingleton('core/session')->setData('amrolepermissions_last_store_id', $storeId);
        }
        else {
            $storeId = Mage::getSingleton('core/session')->getData('amrolepermissions_last_store_id');
            $request->setParam('store', $storeId);
        }

        $id = $request->getParam('id');

        if (!$id) { // New category
            $id = $request->getParam('parent'); // Check for parent permissions
        }

        if (!$id || !in_array($id, $catRestrictions)) {
            $id = $catRestrictions[0];

            $request->setParam('id', $id);
        }
    }
}