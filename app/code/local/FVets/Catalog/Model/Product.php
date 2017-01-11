<?php

class FVets_Catalog_Model_Product extends Mage_Catalog_Model_Product {

    private $qty_increments = null;

    public function getQtyIncrements()
    {
        if (!isset($this->qty_increments))
        {
            $item = Mage::getModel('cataloginventory/stock_item')->getCollection()
                ->addFieldToFilter('stock_id', $this->getStockItem()->getStockId())
                ->addFieldToFilter('product_id', $this->getId())
                ->getFirstItem();
            $this->qty_increments = $item->getQtyIncrements();
        }
        return $this->qty_increments;
    }

    /*public function load($id, $field = null) {


        if(Mage::app()->getStore()->isAdmin())
        {
            return parent::load($id, $field = null);
        }
        if (null !== $field || !Mage::app()->useCache('product_loading')) {
            return parent::load($id, $field);
        }
 
        // Caching product data
        Varien_Profiler::start(__METHOD__);
        $storeId = (int) $this->getStoreId();
        $cacheId = "product-$id-$storeId";
        if ($cacheContent = Mage::app()->loadCache($cacheId)) {
            $data = unserialize($cacheContent);
            if (!empty($data)) {
                foreach ($data as $key => &$value){
                    $this->$key = $value;
                }
                unset($value);
            }
        } else {
            parent::load($id);

            try {
                $cacheContent = serialize(get_object_vars($this));
                $tags = array(
                    Mage_Core_Model_Store::CACHE_TAG,
                    Mage_Core_Model_Store::CACHE_TAG.'_'.Mage::app()->getStore()->getStoreId(),
                    Mage_Core_Model_Website::CACHE_TAG,
                    Mage_Core_Model_Website::CACHE_TAG.'_'.Mage::app()->getStore()->getWebsiteId(),
                    Mage_Catalog_Model_Product::CACHE_TAG,
                    Mage_Catalog_Model_Product::CACHE_TAG.'_'.$id,
                );
                $lifetime = Mage::getStoreConfig('core/cache/lifetime');
                Mage::app()->saveCache($cacheContent, $cacheId, $tags, $lifetime);
            } catch (Exception $e) {
                // Exception = no caching
                Mage::logException($e);
            }
        }
        Varien_Profiler::stop(__METHOD__);
 
        return $this;
    }*/
}
