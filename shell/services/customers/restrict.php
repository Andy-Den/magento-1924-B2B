<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/30/16
 * Time: 3:43 PM
 */

require_once __DIR__ . '/../configService.php';

array_shift($argv);

list($websiteId, $categoryBrand, $restrictOrAllow) = $argv;

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
		updateBrandRestriction($websiteId, $line, $categoryBrand, $restrictOrAllow);
		$totalDeCustomers++;
	}
}

$return = '[Total de clientes no arquivo: ' . $totalDeCustomers . '] ' .
	'[Clientes atualizados: ' . $updatedCustomers . '] ' .
	'[Clientes não encontrados: ' . $notFoundedCustomers . ']';

die($return);

function updateBrandRestriction($websiteId, $customerIdErp, $brandId, $restricted = true)
{
	global $updatedCustomers;
	global $notFoundedCustomers;

	//loadar esse customer pelo ID_ERP:
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
		//se o customer não tem nenhuma restrição e deveria, então:
		if (!$customer->getRestrictedBrands() && $restricted) {
			$customer->setRestrictedBrands($brandId);
			$customer->getResource()->saveAttribute($customer, 'restricted_brands');
			$updatedCustomers++;
		} else {
			$customerRestrictedBrands = $customer->getRestrictedBrands();
			$customerRestrictedBrands = explode(',', $customerRestrictedBrands);

			//se o customer não possui a restrição da marca e deveria, então:
			if (!in_array($brandId, $customerRestrictedBrands) && $restricted) {
				$customerRestrictedBrands[] = (string)$brandId;
				$customer->setRestrictedBrands(implode(',', $customerRestrictedBrands));
				$customer->getResource()->saveAttribute($customer, 'restricted_brands');
				$updatedCustomers++;
			} else {
				//se o customer possui a restrição da marca e NÃO deveria, então:
				if (in_array($brandId, $customerRestrictedBrands) && !$restricted) {
					foreach ($customerRestrictedBrands as $key => $value) {
						if ($brandId == $value) {
							unset($customerRestrictedBrands[$key]);
							break;
						}
					}
					$customer->setRestrictedBrands(implode(',', $customerRestrictedBrands));
					$customer->getResource()->saveAttribute($customer, 'restricted_brands');
					$updatedCustomers++;
				}
			}
		}
	} else {
		$notFoundedCustomers++;
	}
}