<?php


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

//$setup->removeAttribute('customer', 'ignore_promos');

//Add ignore promotions attribute

$entityTypeId = $setup->getEntityTypeId('customer');

$setup->addAttribute($entityTypeId, 'ignore_promos', array(
	'input' => 'select',
	'type' => 'int',
	'label' => 'Ignore Promos',
	'source' => 'eav/entity_attribute_source_boolean',
	'required' => false,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
	'visible' => true,
	'default' => 0
));

$attribute  = Mage::getSingleton("eav/config")->getAttribute("customer", "ignore_promos");


$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";
$attribute->setData("used_in_forms", $used_in_forms)
->setData("is_used_for_customer_segment", true)
->setData("is_system", 0)
->setData("is_user_defined", 1)
->setData("is_visible", 1)
->setData("sort_order", 100)
;
$attribute->save();

//Marcar o atributo ignore_promos para todos os clientes do grupo "doctorsvet_com_excecao".
$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addFieldToFilter('group_id', '11')
;

foreach ($customers as $customer)
{
	$customer->setData('ignore_promos', '1')->getResource()->saveAttribute($customer, 'ignore_promos');
}

$setup->endSetup();