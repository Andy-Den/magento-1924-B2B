<?php

$this->startSetup();

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'fvets_salesrep');
$attribute->setData('used_in_forms', array('adminhtml_customer_integrarep'));
$attribute->save();

$entityTypeId = $this->getEntityTypeId('customer');

$this->addAttribute($entityTypeId, 'restricted_brands', array(
	"type"     => "varchar",
	"backend"  => "eav/entity_attribute_backend_array",
	"label"    => "Restricted Brands",
	"input"    => "multiselect",
	"source"   => "fvets_salesrep/source_brands",
	"visible"  => true,
	"required" => false,
	"default" => "",
	"frontend" => "",
	"unique"     => false,
	"note"       => "As marcas aqui selecionadas não serão apresentadas ao cliente.",
	'backend_type'     => 'varchar',
	'frontend_input'   => 'multiselect',
));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'restricted_brands');
$attribute->setData('used_in_forms', array('adminhtml_customer_integrarep'));
$attribute->save();

$this->endSetup();