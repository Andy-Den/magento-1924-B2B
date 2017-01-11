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
 * Order create sidebar recently view
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Sales_Order_Create_Sidebar_Pviewed 
    extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Pviewed 
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
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        $productCollection = $this->getData('item_collection');
        if (is_null($productCollection)) {
            $stores = array();
            $website = Mage::app()->getStore($this->getStoreId())->getWebsite();
            foreach ($website->getStores() as $store) {
                $stores[] = $store->getId();
            }
            $collection = Mage::getModel('reports/event')
                ->getCollection()
                ->addStoreFilter($stores)
                ->addRecentlyFiler(Mage_Reports_Model_Event::EVENT_PRODUCT_VIEW, $this->getCustomerId(), 0);
            $productIds = array();
            foreach ($collection as $event) {
                $productIds[] = $event->getObjectId();
            }
            $productCollection = null;
            if ($productIds) {
                $productCollection = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->setStoreId($this->getQuote()->getStoreId())
                    ->addStoreFilter($this->getQuote()->getStoreId())
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('price')
                    ->addAttributeToSelect('small_image')
                    ->addIdFilter($productIds);
                $stockId = $this->getWarehouseHelper()
                    ->getAssignmentMethodHelper()
                    ->getQuoteStockId($this->getQuote());
                if ($stockId) {
                    $productCollection->setFlag('stock_id', $stockId);
                }
                $productCollection->load();
            }
            $this->setData('item_collection', $productCollection);
        }
        return $productCollection;
    }
}