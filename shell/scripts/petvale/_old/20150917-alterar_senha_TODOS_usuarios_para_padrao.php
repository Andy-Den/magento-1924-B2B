<?php
require_once './configScript.php';


$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToFilter('website_id', $websiteId);

$changedPassword = array();
$notChangedPassword = array();
foreach ($customers as $customer)
{
	if (strpos($customer->getEmail(),'@4vets.com.br') !== false) {
		continue;
	}

	$logCustomer = Mage::getModel('log/customer')->loadByCustomer($customer->getId());

	$customer->changePassword($defaultCustomerPassword);
	$customer->save();

	echo "+";

}

