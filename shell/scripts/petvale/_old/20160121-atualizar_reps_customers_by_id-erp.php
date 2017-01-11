<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 1/21/16
 * Time: 3:39 PM
 */

require_once './configScript.php';

$websiteId = 6;

$lines = file("extras/20150121_customers_x_reps.csv", FILE_IGNORE_NEW_LINES);
$notFoundedCustomers = 0;
$updatedCustomers = 0;

if (empty($lines))
{
	echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else
{
	foreach ($lines as $key => $value)
	{
		$value = explode('|', $value);

		$idCustomer = $value[0];
		$idSalesrep = $value[1];

		$customer = Mage::getModel('customer/customer')->getCollection()
			->addFieldToFilter('id_erp', $idCustomer)
			->addFieldToFilter('website_id', $websiteId)
			->getFirstItem();

		if ($customer && $customer->getId())
		{
			$customer->setFvetsSalesrep($idSalesrep);
			$customer->getResource()->saveAttribute($customer, 'fvets_salesrep');
			$updatedCustomers++;
		} else {
			$notFoundedCustomers++;
		}
	}

	echo "Customers não encontrados: " . $notFoundedCustomers . "\n";
	echo "Customers atualizados: " . $updatedCustomers . "\n";
}