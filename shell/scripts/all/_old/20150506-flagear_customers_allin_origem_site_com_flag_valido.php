<?php
require_once './configScript.php';

foreach ($websitesId as $websiteId) {

	$customersAlterados = 0;

	$startDate = date('Y-m-d H:i:s', strtotime('2015-04-24' . ' 00:00:01'));

	$customers = Mage::getModel('customer/customer')
		->getCollection()
		->addAttributeToFilter('website_id', $websiteId)
		->addAttributeToFilter('origem', 'SITE')
		->addAttributeToFilter('created_at', array('gteq' => $startDate))
		->addAttributeToFilter('fvets_allin_status', 'I');

	//echo "\n\n\n\n$websiteId\n\n\n\n";
	foreach ($customers as $customer) {
		//echo $customer->getId() . "\n";
		$customer->load();
		$customer->setFvetsAllinStatus('V');
		$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
		$customersAlterados ++;
	}
	echo "WebsiteId: " . $websiteId . " <> Customers Alterados:" . $customersAlterados . "\n";
}