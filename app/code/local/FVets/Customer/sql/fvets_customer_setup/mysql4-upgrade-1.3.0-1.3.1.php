<?php

$this->startSetup();

$entityTypeId = $this->getEntityTypeId('customer');

$this->addAttribute($entityTypeId, 'status_secondary_email', array(
	'input' => 'select',
	'type' => 'varchar',
	'label' => 'Status Email Secundário',
	'source' => 'fvets_allin/source_status',
	'required' => true,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => true,
	'default' => 'I'
));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'status_secondary_email');
$attribute->setData('used_in_forms', array('adminhtml_customer'));
$attribute->save();

$this->endSetup();