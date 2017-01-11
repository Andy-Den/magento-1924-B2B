<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "commission",  array(
	"type"     => "int",
	"backend"  => "",
	"label"    => "Comissão sobre o cliente",
	"input"    => "text",
	"visible"  => true,
	"required" => false,
	"default" => "",
	"frontend" => "",
	"unique"     => false,
	"note"       => "",
	'backend_type'     => 'int',
	'frontend_input'   => 'text',

));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "commission");


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