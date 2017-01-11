<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 8/4/15
 * Time: 4:41 PM
 */

require_once './configScript.php';

$lines = file("extras/20150804-active-allin-customers.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines))
{
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else
{

	foreach ($lines as $email)
	{
		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId($websiteId);
		$customer->loadByEmail($email);

		if ($customer->getId())
		{
			$customer->setData('fvets_allin_status', 'V');
			$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
			echo '+';
		}
	}
}