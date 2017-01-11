<?php

/** @var Mage_Customer_Model_Resource_Setup $this */
$this->startSetup();

// Add customer attributes
$entityTypeId = $this->getEntityTypeId('customer');

$this->addAttribute($entityTypeId, 'crmv', array(
	'input' => 'text',
	'type' => 'varchar',
	'label' => 'CRMV',
	'required' => false,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => true,
	'data_model' => 'magefm_customer/attribute_data_crmv',
));

$this->run("ALTER TABLE `{$this->getTable('sales/quote')}` ADD `customer_crmv` VARCHAR(255) NULL;");

$attributes = array('crmv');
$forms = array('customer_account_edit', 'customer_account_create', 'adminhtml_customer', 'checkout_register');

foreach ($attributes as $attributeCode) {
	$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
	$attribute->setData('used_in_forms', $forms);
	$attribute->save();
}

$this->endSetup();