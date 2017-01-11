<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/30/16
 * Time: 3:43 PM
 */

require_once __DIR__ . '/../configService.php';

array_shift($argv);

list($websiteId, $attributeCode, $value, $attributeFilterCode) = $argv;

//$websiteId = 8;
//$attributeCode = 'fvets_allin_status';
//$value = 0;
//$attributeFilterCode = 'email';


if (count($argv) < 4) {
	die('Todos os campos devem ser preenchidos.');
}

$lines = file($directoryImport . 'import/customers.csv', FILE_IGNORE_NEW_LINES);

$totalDeCustomers = 0;
$updatedCustomers = 0;
$notFoundedCustomers = 0;

if (empty($lines)) {
	die("\n\n" . "arquivo vazio ou não existe" . "\n\n");
} else {
	$totalDeCustomers = 0;
	$updatedCustomers = 0;
	$notFoundedCustomers = 0;
	foreach ($lines as $line) {
		$line = trim($line);
		updateAttributeValue($websiteId, $line, $attributeCode, $value);
		$totalDeCustomers++;
	}
}

$return = '[Total de clientes no arquivo: ' . $totalDeCustomers . '] ' .
	'[Clientes atualizados: ' . $updatedCustomers . '] ' .
	'[Clientes não encontrados: ' . $notFoundedCustomers . ']';

die($return);

function updateAttributeValue($websiteId, $line, $attributeCode, $value = 1)
{
	global $updatedCustomers;
	global $notFoundedCustomers;
	global $attributeFilterCode;

	$filter = null;

	$filter = array(
		array('attribute' => $attributeFilterCode, 'eq' => $line)
	);

	//loadar esse customer pelo ID_ERP:
	$customer = Mage::getModel('customer/customer')
		->getCollection()
		->addAttributeToFilter('website_id', $websiteId)
		->addAttributeToFilter(
			$filter
		)
		->getFirstItem();

	//se ele existe:
	if ($customer->getId()) {
		$customer->load();
		//atualiza o atributo do customer
		if ($attributeCode == 'status') {
			if ($value == 0) {
				$customer->setData('fvets_allin_status', 'I');
				$customer->setBlockAllinStatus(1);
				$customer->setIsActive(0);
				$customer->save();
				$updatedCustomers++;
			} else {
				$customer->setData('fvets_allin_status', 'V');
				$customer->setBlockAllinStatus(V);
				$customer->setIsActive(1);
				$customer->save();
				$updatedCustomers++;
			}
			return;
		}
		if ($attributeCode == 'fvets_allin_status') {
			if ($value == 0) {
				$customer->setData('fvets_allin_status', 'I');
				$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
				$updatedCustomers++;
			} else {
				$customer->setData('fvets_allin_status', 'V');
				$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
				$customer->save();
				$updatedCustomers++;
			}
			return;
		}
		if ($customer->getData($attributeCode) != $value) {
			$customer->setData($attributeCode, $value);
			$customer->getResource()->saveAttribute($customer, $attributeCode);
			$updatedCustomers++;
		}
	} else {
		$notFoundedCustomers++;
	}
}