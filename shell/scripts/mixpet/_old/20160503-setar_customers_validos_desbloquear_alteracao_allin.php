<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 8/4/15
 * Time: 4:41 PM
 */

require_once './configScript.php';

$lines = file("extras/20160503-setar_customers_validos_desbloquear_alteracao_allin.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	$notUpdatedCustomersIdsErp = array();
	$updatedCustomersCount = 0;
	foreach ($lines as $customerIdErp) {
		$customer = Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addAttributeToFilter(
				array(
					array('attribute' => 'id_erp', 'eq' => $customerIdErp)
				)
			)
			->getFirstItem();

		if ($customer->getId()) {
			$customer->setData('fvets_allin_status', 'V');
			$customer->setBlockAllinStatus(0);
			$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
			$customer->getResource()->saveAttribute($customer, 'block_allin_status');
			echo '+';
			$updatedCustomersCount++;
		} else {
			$notUpdatedCustomersIdsErp[] = $customerIdErp;
		}
	}

	echo "\nCustomers atualizados: " . $updatedCustomersCount . "\n";
	echo "IDs_ERP dos Customers não atualizados: " . "\n";
	foreach ($notUpdatedCustomersIdsErp as $notUpdatedCustomerIdsErp) {
		echo $notUpdatedCustomerIdsErp . "\n";
	}
}