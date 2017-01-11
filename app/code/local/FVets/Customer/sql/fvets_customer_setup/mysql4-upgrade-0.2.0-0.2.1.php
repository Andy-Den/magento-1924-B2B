<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "store_view",  array(
	"type"     => "text",
	"backend"  => "eav/entity_attribute_backend_array",
	"label"    => "Store View",
	"input"    => "multiselect",
	"source"   => "fvets_customer/eav_entity_attribute_source_storeview",
	"visible"  => true,
	"required" => true,
	"default" => "",
	"frontend" => "",
	"unique"     => false,
	"note"       => "",
	'backend_type'     => 'varchar',
	'frontend_input'   => 'multiselect',

));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "store_view");


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


//Associate customer to default storeview from your website
$entityTypeId = $installer->getEntityTypeId('customer');
$installer->run("
INSERT INTO customer_entity_text ( entity_type_id, attribute_id, entity_id, value)
SELECT '".$entityTypeId."', '".$attribute->getId()."', c.entity_id, csg.default_store_id
FROM customer_entity as c, core_store_group as csg
WHERE c.website_id = csg.website_id
;");



$installer->endSetup();