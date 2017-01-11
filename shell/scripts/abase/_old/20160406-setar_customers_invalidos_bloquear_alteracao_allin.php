<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 8/4/15
 * Time: 4:41 PM
 */

require_once './configScript.php';

$lines = file("extras/20160406-setar_customers_invalidos_bloquear_alteracao_allin.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines))
{
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else
{
	$notUpdatedCustomersIdsErp = array();
	$updatedCustomersCount = 0;
	foreach ($lines as $customerIdErp)
	{
		$customer = Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addAttributeToFilter(
				array(
					array('attribute' => 'id_erp', 'eq' => '00' . $customerIdErp),
					array('attribute' => 'id_erp', 'eq' => $customerIdErp)
				)
			)
			->getFirstItem();

		if ($customer->getId())
		{
			$customer->setData('fvets_allin_status', 'I');
			$customer->setBlockAllinStatus(1);
			$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
			$customer->getResource()->saveAttribute($customer, 'block_allin_status');
			echo '+';
			$updatedCustomersCount++;
		} else {
			$notUpdatedCustomersIdsErp[] = $customerIdErp;
		}
	}

	echo "Customers atualizados: " . $updatedCustomersCount . "\n";
	echo "IDs_ERP dos Customers não atualizados: " . "\n";
	foreach ($notUpdatedCustomersIdsErp as $notUpdatedCustomerIdsErp) {
		echo $notUpdatedCustomerIdsErp . "\n";
	}
}