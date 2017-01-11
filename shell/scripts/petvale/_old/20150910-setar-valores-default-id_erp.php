<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/10/15
 * Time: 3:20 PM
 */

require_once './configScript.php';

global $readConnection;

//limpa todos os dados do atributo id_erp para o escopo 0 (default);
$delete = "delete from catalog_product_entity_varchar WHERE entity_id IN (
SELECT product_id
FROM catalog_product_website
WHERE website_id = $websiteId) and store_id = 0 and entity_type_id = 4 and attribute_id = 185";

$writeConnection->query($delete);

//seleciona todos os entity_id dos produtos do website em questao;
$query = "select cpe.entity_id from catalog_product_entity cpe
join catalog_product_website cpw on cpw.product_id = cpe.entity_id and cpw.website_id = $websiteId";

$items = $readConnection->fetchAll($query);

foreach($items as $item) {
	//para cada um dos entity_id encontrados insere um valor nulo para o atributo id_erp no escopo 0 (default);
	$insert = "insert ignore into catalog_product_entity_varchar (entity_type_id, attribute_id, store_id, entity_id, value) values (4, 185, 0, {$item['entity_id']}, null)";
	$writeConnection->query($insert);
	echo "+";
}
