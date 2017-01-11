<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 8/4/15
 * Time: 4:41 PM
 */

require_once './configScript.php';

$lines = file("extras/20160412-setar_customers_invalidos_bloquear_alteracao_allin_reps.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	$updatedCustomersCount = 0;
	$storeIds = Mage::getModel('core/website')->load($websiteId)->getStoreIds();
	foreach ($lines as $salesrepIdErp) {
		$salesrep = Mage::getModel('fvets_salesrep/salesrep')
			->getCollection()
			->addFieldToFilter('id_erp', $salesrepIdErp)
			->addStoresToFilter($storeIds)
			->getFirstItem();

		if ($salesrep->getId()) {
			echo "\n" . 'Desabilitando customers de: ' . $salesrep->getName() . "\n";
			$customers = $salesrep->getSelectedCustomersCollection();

			foreach ($customers as $customer) {
				$customer->setData('fvets_allin_status', 'I');
				$customer->setBlockAllinStatus(1);
				$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
				$customer->getResource()->saveAttribute($customer, 'block_allin_status');
				echo '+';
				$updatedCustomersCount++;
			}
		} else {
			$notUpdatedCustomersIdsErp[] = $salesrepIdErp;
		}
	}

	echo "\n" . "Customers atualizados: " . $updatedCustomersCount . "\n";
}