<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 8/4/15
 * Time: 4:41 PM
 */

require_once './configScript.php';

$lines = file("extras/20160420-setar_customers_invalidos_bloquear_alteracao_allin.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	$notFoundedCustomers = array();
	$updatedCustomersCount = 0;
	foreach ($lines as $customerIdErp) {
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

		if ($customer->getId()) {
			$customer->load();
			if (!$customer->getFvetsAllinStatus() || $customer->getFvetsAllinStatus() == 'V') {
				$customer->setData('fvets_allin_status', 'I');
				$customer->setBlockAllinStatus(1);
				$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
				$customer->getResource()->saveAttribute($customer, 'block_allin_status');
				$updatedCustomersCount++;
				echo '+';
			} else {
				echo '-';
			}
		} else {
			$notFoundedCustomers[] = $customerIdErp;
		}
	}

	echo "\nCustomers atualizados para inválidos: " . $updatedCustomersCount . "\n";
	echo "IDs_ERP dos Customers não encontrados: " . "\n";
	foreach ($notFoundedCustomers as $notFoundedCustomer) {
		echo $notFoundedCustomer . "\n";
	}
}

$lines = file("extras/20160420-setar_customers_validos_desbloquear_alteracao_allin.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	$notFoundedCustomers = array();
	$updatedCustomersCount = 0;
	foreach ($lines as $customerIdErp) {
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

		if ($customer->getId()) {
			$customer->load();
			if (!$customer->getFvetsAllinStatus() || $customer->getFvetsAllinStatus() == 'I') {
				$customer->setData('fvets_allin_status', 'V');
				$customer->setBlockAllinStatus(0);
				$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
				$customer->getResource()->saveAttribute($customer, 'block_allin_status');
				$updatedCustomersCount++;
				echo '+';
			} else {
				echo '-';
			}
		} else {
			$notFoundedCustomers[] = $customerIdErp;
		}
	}

	echo "\nCustomers atualizados para ativos: " . $updatedCustomersCount . "\n";
	echo "IDs_ERP dos Customers não encontrados: " . "\n";
	foreach ($notFoundedCustomers as $notFoundedCustomer) {
		echo $notFoundedCustomer . "\n";
	}
}