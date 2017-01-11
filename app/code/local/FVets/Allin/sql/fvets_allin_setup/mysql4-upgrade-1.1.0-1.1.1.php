<?php

$this->startSetup();

$entityTypeId = $this->getEntityTypeId('customer');

$this->addAttribute($entityTypeId, 'block_allin_status', array(
	'input' => 'select',
	'type' => 'int',
	'label' => 'Block change allin status?',
	'source' => 'eav/entity_attribute_source_boolean',
	'required' => false,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => true,
	'default' => 1
));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'block_allin_status');
$attribute->setData('used_in_forms', array('adminhtml_customer'));
$attribute->save();

$this->endSetup();