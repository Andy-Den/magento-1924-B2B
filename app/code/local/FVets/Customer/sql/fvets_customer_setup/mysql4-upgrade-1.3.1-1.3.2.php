<?php

$this->startSetup();

$entityTypeId = $this->getEntityTypeId('customer');

// Cria o atribute para definir um parâmetro de data da ultima compra e analisar se o cliente deve ser
// com origem Site ou ERP
$this->addAttribute($entityTypeId, 'last_sale_erp', array(
	'input' => 'text',
	'type' => 'varchar',
	'label' => 'Data da última compra no ERP/Site',
	'required' => false,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => false,
    'readonly' => true,
));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'last_sale_erp');
$attribute->setData('used_in_forms', array());
$attribute->save();

$this->endSetup();