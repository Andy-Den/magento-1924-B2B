<?php


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$entityTypeId = $setup->getEntityTypeId('customer');

$setup->addAttribute($entityTypeId, 'automatic_premier_policy', array(
	'input' => 'select',
	'type' => 'int',
	'label' => 'Automatically Choose Premier Commercial Policy',
	'source' => 'eav/entity_attribute_source_boolean',
	'required' => false,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => true,
	'default' => 0,
	'note' => 'O sistema irá, todo o primeiro dia do mês à meia noite, recalcular as promoções deste cliente com base no mês anterior.'
));

$attribute  = Mage::getSingleton("eav/config")->getAttribute("customer", "automatic_premier_policy");


$used_in_forms=array();

$used_in_forms[]="adminhtml_customer_premier";
$attribute->setData("used_in_forms", $used_in_forms)
->setData("is_used_for_customer_segment", true)
->setData("is_system", 0)
->setData("is_user_defined", 1)
->setData("is_visible", 1)
->setData("sort_order", 100)
;
$attribute->save();

$setup->endSetup();