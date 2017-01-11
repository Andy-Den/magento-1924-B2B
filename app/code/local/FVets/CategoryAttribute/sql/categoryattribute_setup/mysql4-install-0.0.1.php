<?php

$this->startSetup();

$this->addAttribute('catalog_category', 'remove_title', array(
	'group'         						=> 'General Information',
	'input'         						=> 'select',
	'type'          						=> 'int',
	'label'         						=> 'Remove Title',
	'source'  								=> 'eav/entity_attribute_source_boolean',
	'visible'       						=> true,
	'visible_on_front'						=> false,
	'wysiwyg_enabled' 						=> false,
	'is_html_allowed_on_front'				=> false,
	'required'      						=> false,
	'default'								=> '0',
	'user_defined' 							=> false,
	'global'        						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$productEntityTypeId = $this->getEntityTypeId('catalog_category');

$this->endSetup();

?>