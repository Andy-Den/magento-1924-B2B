<?php

/** @var Mage_Customer_Model_Resource_Setup $this */
$this->startSetup();

// Add customer attributes
$entityTypeId = $this->getEntityTypeId('customer');

$this->addAttribute($entityTypeId, 'email_password_recovery', array(
	'input' => 'select',
	'type' => 'int',
	'source' => 'eav/entity_attribute_source_boolean',
	'label' => 'email_password_recovery',
	'required' => true,
	'default' => false,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => false,
));

$this->endSetup();