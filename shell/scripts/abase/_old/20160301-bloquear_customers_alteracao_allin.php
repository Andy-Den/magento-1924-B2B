<?php
/**
 * Created by PhpStorm.
 * User: douglas
 * Date: 8/4/15
 * Time: 4:41 PM
 */

require_once './configScript.php';


$customers = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToFilter('block_allin_status', 1);

foreach ($customers as $customer)
{
	$customer->setBlockAllinStatus(0);
	$customer->getResource()->saveAttribute($customer, 'block_allin_status');

	echo '+';
}
echo "\n";

$lines = file("extras/20160229-customers_to_be_disabled_allin.csv", FILE_IGNORE_NEW_LINES);

$array = array();

if (empty($lines))
{
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else
{

	foreach ($lines as $email)
	{
		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId($websiteId);
		$customer->loadByEmail(strtolower($email));

		if ($customer->getId() && !in_array($customer->getId(), $array))
		{
			$customer->setData('block_allin_status', 1);
			$customer->getResource()->saveAttribute($customer, 'block_allin_status');

			$array[] = $customer->getId();
			echo '+';
		}
		else
		{
			//echo '-';
		}
	}
}