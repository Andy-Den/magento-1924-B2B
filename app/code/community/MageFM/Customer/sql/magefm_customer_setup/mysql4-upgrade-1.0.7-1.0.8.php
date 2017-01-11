<?php

/** @var Mage_Customer_Model_Resource_Setup $this */
$this->startSetup();

// Add customer attributes
$entityTypeId = $this->getEntityTypeId('customer');

$this->addAttribute($entityTypeId, 'origem', array(
	'input' => 'hidden',
	'type' => 'varchar',
	'label' => 'Origem',
	'required' => false,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => false,
	'default' => 'SITE'
));

$attributes = array('origem');
$forms = array('adminhtml_customer');

foreach ($attributes as $attributeCode) {
	$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
	$attribute->setData('used_in_forms', $forms);
	$attribute->save();
}

$this->endSetup();