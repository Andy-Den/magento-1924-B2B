<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 6/13/16
 * Time: 11:31 AM
 */

require_once __DIR__ . '/../../configService.php';

array_shift($argv);

list($websiteId, $restrictionGroupId, $restrictOrAllow) = $argv;

if (count($argv) < 3) {
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
		updateCustomerRestrictionGroup($line, $restrictionGroupId, $restrictOrAllow);
		$totalDeCustomers++;
	}
}

$return = '[Total de clientes no arquivo: ' . $totalDeCustomers . '] ' .
	'[Clientes atualizados: ' . $updatedCustomers . '] ' .
	'[Clientes não encontrados: ' . $notFoundedCustomers . ']';

die($return);

function updateCustomerRestrictionGroup($customerIdErp, $groupId, $restricted = true)
{
	global $websiteId;
	global $updatedCustomers;
	global $notFoundedCustomers;

	//loadar esse cara pelo ID_ERP:
	$customer = Mage::getModel('customer/customer')
		->getCollection()
		->addAttributeToFilter('website_id', $websiteId)
		->addAttributeToFilter(
			array(
				array('attribute' => 'id_erp', 'eq' => str_pad($customerIdErp, 6, "0", STR_PAD_LEFT)),
				array('attribute' => 'id_erp', 'eq' => $customerIdErp)
			)
		)
		->getFirstItem();

	//se ele existe:
	if ($customer->getId()) {
		$customer->load();

		$helper = Mage::helper('fvets_catalogrestrictiongroup/customer');
		$customerGroups = $helper->getSelectedCatalogrestrictiongroups($customer);

		$resultRestrictionsGroups = array();
		foreach($customerGroups as $customerGroup) {
			$resultRestrictionsGroups[$customerGroup->getId()] = array();
		}

		if ($restricted) {
			$resultRestrictionsGroups[$groupId] = array();
		} else {
			unset($resultRestrictionsGroups[$groupId]);
		}

		//atualiza o grupo de restrição desse cara:
		Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_customer')->saveCustomerRelation($customer, $resultRestrictionsGroups);
		$updatedCustomers++;
	} else {
		$notFoundedCustomers++;
	}
}