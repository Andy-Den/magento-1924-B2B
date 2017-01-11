<?php

$this->startSetup();

$attribute  = Mage::getSingleton("eav/config")->getAttribute("customer", "ignore_promos");


$used_in_forms=array();

$used_in_forms[]="adminhtml_customer_promo_form";
$attribute->setData("used_in_forms", $used_in_forms)
	->setData("is_used_for_customer_segment", true)
	->setData("is_system", 0)
	->setData("is_user_defined", 1)
	->setData("is_visible", 1)
	->setData("sort_order", 100)
;
$attribute->save();

$this->endSetup();