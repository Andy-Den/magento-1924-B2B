<?php
/**
 * @copyright   Copyright (c) 2009-14 Amasty
 */
class Amasty_Promo_Block_Items extends Mage_Core_Block_Template
{
    public function getFormActionUrl()
    {
        $returnUrl = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));

        $params = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core')->urlEncode($returnUrl)
        );

        return $this->getUrl('ampromo/cart/update', $params);
    }

    protected function _prepareLayout()
    {
        $products = Mage::helper('ampromo')->getNewItems();
        $this->setNewItems($products);
    }
}
