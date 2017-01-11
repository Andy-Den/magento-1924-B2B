<?php

$this->startSetup();

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'restricted_brands');

$attribute
	->setData("is_used_for_customer_segment", true)
	->setData("is_system", 0)
	->setData("is_user_defined", 0)
	->setData("is_visible", 1)
	->setData("sort_order", 100)
;

$attribute->save();

$this->endSetup();