<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/12/15
 * Time: 10:15 AM
 */
require_once './configScript.php';

$websiteId = 5;

$lines = file("extras/reps_doctorsvet.csv", FILE_IGNORE_NEW_LINES);

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

		$customer = Mage::getModel('customer/customer')->load($idCustomer);
		$customer->setFvetsSalesrep($idSalesrep);
		$customer->getResource()->saveAttribute($customer, 'fvets_salesrep');
	}
}