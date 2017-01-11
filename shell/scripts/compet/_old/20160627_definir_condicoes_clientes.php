<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/11/16
 * Time: 3:39 PM
 */

require_once './configScript.php';

$lines = file("extras/20160627_definir_condicoes_clientes.csv", FILE_IGNORE_NEW_LINES);

$fileArray = array();
$stores = Mage::getModel('core/website')->load($websiteId)->getStoreIds();
$defaultCondition = '14/21/28/35/42/49/56/63/70 DIA, 21/28/35/42/49/56/63 DIAS, 21/35/49/63 DIAS, 21/42/63 DIAS, 28/35/42/49/56 DIAS, 28/42/56 DIAS, 28/56 DIAS, 35/42/49 DIAS, 42 DIAS';
$notFoundedConditions = array();
$notFoundedCustomers = array();

$conditionCustomers = array();
if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	foreach ($lines as $line) {
		$lineArray = explode('|', $line);
		$customerIdErp = $lineArray[0];
		$conditions = $lineArray[1];
		$prazoMedio = $lineArray[2];

		$arrayCustomers = array();

		$customer = Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addAttributeToFilter('id_erp', $customerIdErp)
			->getFirstItem();

		if (!$customer->getId()) {
			$notFoundedCustomers[$customerIdErp] = $customerIdErp;
			continue;
		}

		if (strpos($conditions, 'ALTERAR PRAZO') !== false) {
			$conditions = $defaultCondition;
		}

		foreach (explode(',', $conditions) as $conditionName) {
			$conditionName = rtrim($conditionName, ' ');
			$conditionName = ltrim($conditionName, ' ');

			$condition = Mage::getModel('fvets_payment/condition')
				->getCollection()
				->addStoreFilter($stores)
				->addFieldToFilter('name', $conditionName)
				->getFirstItem();

			if (!$condition->getEntityId()) {
				$notFoundedConditions[$conditionName] = $conditionName;
				continue;
			} else {
				$conditionCustomers[$conditionName][$customer->getId()] = array('position' => "");
			}
		}
	}

	foreach ($conditionCustomers as $conditionName => $customers) {
		$condition = Mage::getModel('fvets_payment/condition')
			->getCollection()
			->addStoreFilter($stores)
			->addFieldToFilter('name', $conditionName)
			->getFirstItem();

		$condition->setCustomersData($customers);
		$customerRelation = Mage::getModel('fvets_payment/condition_customer');
		$customerRelation->saveConditionRelation($condition);
		echo "+";
	}

	echo "\n\n";
	echo "Clientes não encontrados: " . count($notFoundedCustomers) . "\n";
	foreach ($notFoundedCustomers as $customerIdErp) {
		echo $customerIdErp . "\n";
	}
	echo "Condições não encontrados: " . count($notFoundedConditions) . "\n";
	foreach ($notFoundedConditions as $conditionName) {
		echo $conditionName . "\n";
	}
}