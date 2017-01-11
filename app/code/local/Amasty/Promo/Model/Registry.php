<?php
/**
 * @copyright   Copyright (c) 2009-14 Amasty
 */
class Amasty_Promo_Model_Registry
{
    protected $_itemsPrepared = false;
    protected $_locked = false;

    public function addPromoItem($sku, $qty)
    {
        if ($this->_locked)
            return;

        $items = Mage::getSingleton('checkout/session')->getAmpromoItems();
        if ($items === null)
            $items = array();

        $autoAdd = false;

        if (Mage::getStoreConfigFlag('ampromo/general/auto_add'))
        {
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(array('status'))
                ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->addAttributeToFilter('sku', $sku)
                ->setPage(1,1);

            /** @var Mage_Catalog_Model_Product $product */
            $product = $collection->getFirstItem();

            if (!$product || !$product->isSalable())
            {
                $hlp = Mage::helper('ampromo');
                $hlp->showMessage($hlp->__('We apologise, but your free gift is not available at the moment'));
            }
            else
            {
				if (Mage::getStoreConfigFlag('ampromo/general/product_with_custom_options')) {
					if (in_array($product->getTypeId(), array('simple', 'virtual')))
						$autoAdd = true;
				} else {
					if (in_array($product->getTypeId(), array('simple', 'virtual')) && !$product->getTypeInstance(true)->hasRequiredOptions($product))
						$autoAdd = true;
				}

            }
        }

        if (isset($items[$sku]))
        {
            $items[$sku]['qty'] += $qty;
        }
        else
        {
            $items[$sku] = array(
                'sku' => $sku,
                'qty' => $qty,
                'auto_add' => $autoAdd
            );
        }

        Mage::getSingleton('checkout/session')->setAmpromoItems($items);
    }

    public function getPromoItems()
    {
        return Mage::getSingleton('checkout/session')->getAmpromoItems();
    }

    public function reset()
    {
        if ($this->_itemsPrepared) {
            $this->_locked = true;
            return;
        }

        $this->_itemsPrepared = true;

        Mage::getSingleton('checkout/session')->setAmpromoItems(array());
    }

    public function getLimits()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        $allowed = $this->getPromoItems();

        foreach ($quote->getItemsCollection() as $item)
        {
            if ($item->isDeleted()) {
                continue;
            }
            /** @var Mage_Sales_Model_Quote_Item $item */

            $sku = $item->getProduct()->getData('sku');

            if (($item->getIsFree()) && isset($allowed[$sku]))
            {
                $allowed[$sku]['qty'] -= $item->getQty();

                if ($allowed[$sku]['qty'] <= 0)
                    unset($allowed[$sku]);
            }
        }

        return $allowed;
    }

    public function deleteProduct($sku)
    {
        $deletedItems = Mage::getSingleton('checkout/session')->getAmpromoDeletedItems();

        if (!$deletedItems)
            $deletedItems = array();

        $deletedItems[$sku] = true;

        Mage::getSingleton('checkout/session')->setAmpromoDeletedItems($deletedItems);
    }

    public function restore($sku)
    {
        $deletedItems = Mage::getSingleton('checkout/session')->getAmpromoDeletedItems();

        if (!$deletedItems || !isset($deletedItems[$sku]))
            return;

        unset($deletedItems[$sku]);

        Mage::getSingleton('checkout/session')->setAmpromoDeletedItems($deletedItems);
    }

    public function getDeletedItems()
    {
        $deletedItems = Mage::getSingleton('checkout/session')->getAmpromoDeletedItems();

        if (!$deletedItems)
            $deletedItems = array();

        return $deletedItems;
    }
}
