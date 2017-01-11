<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 8/4/15
 * Time: 4:41 PM
 */

require_once './configScript.php';

$lines = file("extras/20160229-customers_to_be_disabled_allin.csv", FILE_IGNORE_NEW_LINES);

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

		if ($customer->getId())
		{
			$customer->setData('fvets_allin_status', 'I');
			$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
			echo '+';
		}
	}
}