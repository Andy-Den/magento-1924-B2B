<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/16/15
 * Time: 2:34 PM
 */

require_once './configScript.php';

$customers = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToFilter('website_id', $websiteId);

$count = 0;

foreach ($customers as $customer)
{
	$passwordDefault = $defaultCustomerPassword;
	$customer->load();
	if (!validateHash($passwordDefault, $customer->getPasswordHash()))
	{
		echo $customer->getEmail() . "\n";
		$count++;
	}
}

echo $count;

function validateHash($password, $hash)
{
	$hashArr = explode(':', $hash);
	return md5($hashArr[1] . $password) === $hashArr[0];
}