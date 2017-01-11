<?php

$this->startSetup();

$entityTypeId = $this->getEntityTypeId('customer');

$this->addAttribute($entityTypeId, 'fvets_allin_status', array(
	'input' => 'select',
	'type' => 'int',
	'label' => 'Status Allin',
	'source' => 'fvets_allin/source_status',
	'required' => false,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => true,
	'default' => 0
));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'fvets_allin_status');
$attribute->setData('used_in_forms', array('adminhtml_customer'));
$attribute->save();

$this->endSetup();