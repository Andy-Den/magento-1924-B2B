<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('customer');
$idAttribute = $installer->getAttribute($entityTypeId, 'fvets_salesrep', 'attribute_id');

//Update attribute
$installer->run("
UPDATE eav_attribute SET
entity_type_id = '".$entityTypeId."',
attribute_model = NULL,
backend_model = 'eav/entity_attribute_backend_array',
backend_type = 'varchar',
backend_table = NULL,
frontend_model = NULL,
frontend_input = 'multiselect',
frontend_class = NULL
WHERE attribute_id = '".$idAttribute."';
");

//Insert int values to varchar values
$installer->run("
INSERT INTO customer_entity_varchar ( entity_type_id, attribute_id, entity_id, value)
SELECT entity_type_id, attribute_id, entity_id, value
FROM customer_entity_int
WHERE attribute_id = ".$idAttribute.";
");

//Delete int values
$installer->run("
DELETE FROM customer_entity_int
WHERE entity_type_id = ".$entityTypeId." and attribute_id = ".$idAttribute.";
");

$installer->endSetup();
?>