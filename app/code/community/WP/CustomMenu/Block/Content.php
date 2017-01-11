<?php

class WP_CustomMenu_Block_Content extends Mage_Core_Block_Template
{
    protected function _construct() {
        parent::_construct();

        if (!$categories_key = Mage::registry('menu_cache_key')) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $cache_key = md5($customer->getFvetsSalesrep() . '-' . $customer->getRestrictedBrands() . '-' . Mage::app()->getStore()->getStoreId());
            Mage::register('menu_cache_key', $categories_key);
        }

        $this->addData(array(
            'cache_lifetime' => 7200,
            'cache_tags'     => array(Mage_Catalog_Model_Category::CACHE_TAG),
            'cache_key'      => get_class($this) . '_' . $cache_key
        ));

    }
}