<?php
require_once './configScript.php';

$customers = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToFilter('website_id', $websiteId);

foreach ($customers as $customer) {
	$customer->load();
	if ($customer->getFvetsAllinStatus() != 'V') {
		$customer->setFvetsAllinStatus('V');
		$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
		echo "+";
	}
}
