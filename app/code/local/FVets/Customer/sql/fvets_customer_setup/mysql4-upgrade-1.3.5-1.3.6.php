<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "exporter",  array(
	"type"     => "int",
	"backend"  => "",
	"label"    => "Define se o usuário deve ser exportado",
	"input"    => "text",
	"visible"  => false,
	"required" => false,
	"default" => "",
	"frontend" => "",
	"unique"     => false,
	"note"       => "",
	'backend_type'     => 'varchar',
	'frontend_input'   => 'text',

));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "exporter");


$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$attribute->setData("used_in_forms", $used_in_forms)
	->setData("is_used_for_customer_segment", true)
	->setData("is_system", 0)
	->setData("is_user_defined", 1)
	->setData("is_visible", 0)
	->setData("sort_order", 100)
;
$attribute->save();

$installer->endSetup();