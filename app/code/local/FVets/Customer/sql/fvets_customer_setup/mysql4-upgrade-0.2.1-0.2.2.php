<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "hive_of_activity",  array(
	"type"     => "text",
	"backend"  => "eav/entity_attribute_backend_array",
	"label"    => "Ramo de atividade",
	"input"    => "multiselect",
	"source"   => "fvets_customer/eav_entity_attribute_source_hiveactivity",
	"visible"  => true,
	"required" => true,
	"default" => "",
	"frontend" => "",
	"unique"     => false,
	"note"       => "",
	'backend_type'     => 'varchar',
	'frontend_input'   => 'multiselect',

));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "hive_of_activity");


$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$attribute->setData("used_in_forms", $used_in_forms)
	->setData("is_used_for_customer_segment", true)
	->setData("is_system", 0)
	->setData("is_user_defined", 1)
	->setData("is_visible", 1)
	->setData("sort_order", 100)
;
$attribute->save();

$installer->endSetup();