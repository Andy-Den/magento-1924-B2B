<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (http://www.amasty.com)
 */

class Amasty_Promo_Model_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    protected $_isFree = null;

    public function getIsFree()
    {
        if (is_null($this->_isFree))
        {
            $buyRequest = $this->getBuyRequest();

            $this->_isFree = isset($buyRequest['options']['ampromo_free']);
        }

        return $this->_isFree;
    }

    public function setPrice($v)
    {
        if ($this->getIsFree())
            $v = 0;

        return parent::setPrice($v);
    }

    public function representProduct($product)
    {
        if (parent::representProduct($product))
        {
            $option = $product->getCustomOption('info_buyRequest');
            $productBuyRequest = new Varien_Object($option ? unserialize($option->getValue()) : null);

            $currentBuyRequest = $this->getBuyRequest();

            $productIsFree = isset($productBuyRequest['options']['ampromo_free']);
            $currentIsFree = isset($currentBuyRequest['options']['ampromo_free']);

            return $productIsFree === $currentIsFree;
        }
        else
            return false;
    }
}