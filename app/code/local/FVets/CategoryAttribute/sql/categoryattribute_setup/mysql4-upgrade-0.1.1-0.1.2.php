<?php

$this->startSetup();

$this->addAttribute('catalog_category', 'embedded_title', array(
	'group'         						=> 'General Information',
	'input'         						=> 'text',
	'type'          						=> 'varchar',
	'label'         						=> 'Embedded title',
	'visible'       						=> true,
	'visible_on_front'						=> true,
	'wysiwyg_enabled' 						=> false,
	'is_html_allowed_on_front'				=> false,
	'required'      						=> false,
	'default'								=> '',
	'user_defined' 							=> false,
	'global'        						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$productEntityTypeId = $this->getEntityTypeId('catalog_category');

$this->addAttribute('catalog_category', 'embedded', array(
	'group'         						=> 'General Information',
	'input'         						=> 'textarea',
	'type'          						=> 'text',
	'label'         						=> 'Embedded',
	'visible'       						=> true,
	'visible_on_front'						=> true,
	'wysiwyg_enabled' 						=> true,
	'is_html_allowed_on_front'				=> true,
	'required'      						=> false,
	'default'								=> '',
	'user_defined' 							=> false,
	'global'        						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$productEntityTypeId = $this->getEntityTypeId('catalog_category');

$this->endSetup();

?>