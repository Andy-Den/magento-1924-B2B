<?php

$installer = $this;
$installer->startSetup();

$attribute  = array(
	'type' => 'text',
	'label'=> 'Tipo de Categoria',
	'input' => 'multiselect',
	'backend' => 'eav/entity_attribute_backend_array',
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => true,
	'required' => false,
	'user_defined' => true,
	'default' => "",
	'group' => "General Information",
	'source' => 'fvets_catalog/source_tipoCategoria'
);

$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'type', $attribute);
$installer->endSetup();