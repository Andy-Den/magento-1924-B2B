<?php

if(Mage::helper('core')->isModuleEnabled('DS_PrivateSales')){
	class WP_CustomMenu_Block_Topmenu_Tmp extends DS_PrivateSales_Block_Topmenu {}
} else {
	if (!Mage::getStoreConfig('custom_menu/general/enabled')) {
		class WP_CustomMenu_Block_Topmenu_Tmp extends Mage_Page_Block_Html_Topmenu {}
	} else {
		class WP_CustomMenu_Block_Topmenu_Tmp extends WP_CustomMenu_Block_Navigation {}
	}
}

class WP_CustomMenu_Block_Topmenu extends WP_CustomMenu_Block_Topmenu_Tmp {}