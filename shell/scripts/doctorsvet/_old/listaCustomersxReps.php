<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 2/26/15
 * Time: 4:35 PM
 */
require_once './configIntegra.php';

$query = 'SELECT cev2.value, ce.email, fs.name
FROM customer_entity ce
JOIN customer_entity_varchar cev ON cev.entity_id = ce.entity_id AND cev.attribute_id = (
SELECT ea.attribute_id
FROM eav_attribute ea
WHERE ea.attribute_code = \'fvets_salesrep\' AND ea.entity_type_id = 1)
join customer_entity_varchar cev2 on cev2.entity_id = ce.entity_id and cev2.attribute_id = (
SELECT ea.attribute_id
FROM eav_attribute ea
WHERE ea.attribute_code = \'razao_social\' AND ea.entity_type_id = 1)
JOIN fvets_salesrep fs ON fs.id = cev.value
WHERE ce.website_id = 5';

$customerCollection = $readConnection->fetchAll($query);

foreach($customerCollection as $customer) {
	echo $customer['value'] . '|' . $customer['email'] . '|' . $customer['name'];
	echo "\n";
}