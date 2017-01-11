<?php

require_once './configScript.php';

$customers = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToFilter('website_id', $websiteId)
	->addAttributeToFilter('fvets_allin_status', 'I');

foreach ($customers as $customer) {
	$customer->setIsActive(0);
	$customer->save();
	echo "+";
}