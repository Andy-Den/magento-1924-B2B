<?php

require_once '../../../app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');
/**
 * Inicia a integração de produtos atualizando o status (quantidade e disponibilidade),
 */

$updates = array(

	array('attribute_id' => 71, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //Name
	array('attribute_id' => 71, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //Name
	array('attribute_id' => 71, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //Name

	array('attribute_id' => 71, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //Name

	array('attribute_id' => 71, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //Name
	array('attribute_id' => 71, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //Name
	array('attribute_id' => 71, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //Name
	array('attribute_id' => 71, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //Name


	array('attribute_id' => 185, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //ID ERP
	array('attribute_id' => 185, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //ID ERP
	array('attribute_id' => 185, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //ID ERP

	array('attribute_id' => 185, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //ID ERP

	array('attribute_id' => 185, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //ID ERP
	array('attribute_id' => 185, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //ID ERP
	array('attribute_id' => 185, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //ID ERP
	array('attribute_id' => 185, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_varchar'), //ID ERP

	array('attribute_id' => 75, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //price
	array('attribute_id' => 75, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //price
	array('attribute_id' => 75, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //price

	array('attribute_id' => 75, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //price

	array('attribute_id' => 75, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //price
	array('attribute_id' => 75, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //price
	array('attribute_id' => 75, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //price
	array('attribute_id' => 75, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //price

	array('attribute_id' => 76, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //special price
	array('attribute_id' => 76, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //special price
	array('attribute_id' => 76, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //special price

	array('attribute_id' => 76, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //special price

	array('attribute_id' => 76, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //special price
	array('attribute_id' => 76, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //special price
	array('attribute_id' => 76, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //special price
	array('attribute_id' => 76, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_decimal'), //special price

	array('attribute_id' => 77, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special from date
	array('attribute_id' => 77, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special from date
	array('attribute_id' => 77, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special from date

	array('attribute_id' => 77, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special from date

	array('attribute_id' => 77, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special from date
	array('attribute_id' => 77, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special from date
	array('attribute_id' => 77, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special from date
	array('attribute_id' => 77, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special from date

	array('attribute_id' => 78, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 78, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 78, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date

	array('attribute_id' => 78, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date

	array('attribute_id' => 78, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 78, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 78, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 78, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date

	array('attribute_id' => 93, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 93, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 93, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date

	array('attribute_id' => 93, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date

	array('attribute_id' => 93, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 93, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 93, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date
	array('attribute_id' => 93, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //special to date

	array('attribute_id' => 94, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //news_to_date
	array('attribute_id' => 94, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //news_to_date
	array('attribute_id' => 94, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //news_to_date

	array('attribute_id' => 94, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //news_to_date

	array('attribute_id' => 94, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //news_to_date
	array('attribute_id' => 94, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //news_to_date
	array('attribute_id' => 94, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //news_to_date
	array('attribute_id' => 94, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_datetime'), //news_to_date

	array('attribute_id' => 96, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //status
	array('attribute_id' => 96, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //status
	array('attribute_id' => 96, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //status

	array('attribute_id' => 96, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //status

	array('attribute_id' => 96, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //status
	array('attribute_id' => 96, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //status
	array('attribute_id' => 96, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //status
	array('attribute_id' => 96, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //status

	array('attribute_id' => 102, 'from_store' => 0, 'to_store' => 8, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //visibility
	array('attribute_id' => 102, 'from_store' => 0, 'to_store' => 9, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //visibility
	array('attribute_id' => 102, 'from_store' => 0, 'to_store' => 10, 'to_website' => 5, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //visibility

	array('attribute_id' => 102, 'from_store' => 0, 'to_store' => 2, 'to_website' => 2, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //visibility

	array('attribute_id' => 102, 'from_store' => 0, 'to_store' => 4, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //visibility
	array('attribute_id' => 102, 'from_store' => 0, 'to_store' => 5, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //visibility
	array('attribute_id' => 102, 'from_store' => 0, 'to_store' => 6, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //visibility
	array('attribute_id' => 102, 'from_store' => 0, 'to_store' => 7, 'to_website' => 4, 'entity_type' => 4, 'table' => 'catalog_product_entity_int'), //visibility
);

foreach ($updates as $update) {

	$attributeId = $update['attribute_id'];
	$fromStore = $update['from_store'];
	$toStore = $update['to_store'];
	$toWebsite = $update['to_website'];
	$entityType = $update['entity_type'];
	$table = $update['table'];

//produtos que estão na store 1 e não na 2
	$query = "select * from " . $table . " cpev
					JOIN catalog_product_website cpw ON cpw.website_id = " . $toWebsite . " AND cpw.product_id = cpev.entity_id where
					cpev.attribute_id = " . $attributeId . " and cpev.store_id = " . $fromStore
		. " and cpev.entity_id not in " . "(select cpev2.entity_id from " . $table . " cpev2
					JOIN catalog_product_website cpw ON cpw.website_id = " . $toWebsite . " AND cpw.product_id = cpev2.entity_id where
					cpev2.attribute_id = " . $attributeId . " and cpev2.store_id = " . $toStore . " and cpev2.entity_type_id = " . $entityType . ") and cpev.entity_type_id = " . $entityType.";";

	echo $query . "\n";

	$results = $readConnection->fetchAll($query);

//insere os registros faltosos para 2
	foreach ($results as $result) {

		if (trim($result['value']) != '') {

			$query = "insert into " . $table . " (entity_type_id, attribute_id, store_id, entity_id, value) values (" . $entityType . ", " . $attributeId . ", " . $toStore . ", " . $result['entity_id'] . ", '" . $result['value'] . "');";

			echo $query . "\n";

			$writeConnection->query($query);
		}
	}

//seleciona todos os registros da store 0 para o atributo em questão para que sejam removidos
	$query = "select * from " . $table . " cpev
					JOIN catalog_product_website cpw ON cpw.website_id = " . $toWebsite . " AND cpw.product_id = cpev.entity_id where
					cpev.attribute_id = " . $attributeId . " and cpev.store_id = " . $fromStore . " and cpev.entity_type_id = " . $entityType.";";

	echo $query . "\n";

	$results = $readConnection->fetchAll($query);



//remove cada um dos registros da store 0 para o atributo em questão
//	foreach ($results as $result) {
//		$query = "delete from " . $table . " where attribute_id = " . $attributeId . " and store_id = " . $fromStore . " and entity_id = " . $result['entity_id'] . " and entity_type_id = " . $entityType;
//		$writeConnection->query($query);
//		echo $query;
//	}

}
