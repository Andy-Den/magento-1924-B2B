<?php

if(Mage::helper('core')->isModuleEnabled('MageFM_Customer')){
	class MageFM_Customer_Block_Checkout_Onepage_Progress_Tmp extends  Innoexts_Warehouse_Block_Checkout_Onepage_Progress  {}
} else {
	class MageFM_Customer_Block_Checkout_Onepage_Progress_Tmp extends Mage_Checkout_Block_Onepage_Progress {}
}

class MageFM_Customer_Block_Checkout_Onepage_Progress extends MageFM_Customer_Block_Checkout_Onepage_Progress_Tmp
{

    protected function _getStepCodes()
    {
        return array('login', 'customer', 'billing', 'shipping', 'shipping_method', 'payment', 'review');
    }

}