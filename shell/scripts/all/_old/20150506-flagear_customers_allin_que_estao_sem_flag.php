<?php
require_once './configScript.php';

foreach ($websitesId as $websiteId) {

	$customersAlterados = 0;

	$customers = Mage::getModel('customer/customer')
		->getCollection()
		->addAttributeToFilter('website_id', $websiteId);

	$customers->getSelect()
		->joinleft(
			array('cev' => 'customer_entity_varchar'),
			"cev.entity_id = e.entity_id and cev.attribute_id = (select attribute_id from eav_attribute where attribute_code = 'fvets_allin_status')",
			array('cev.value as allin_status')
		)
		->where('cev.value is null');

	$customers->getSelect()->__toString();

	foreach ($customers as $customer) {
		//echo $customer->getId() . "\n";
		$customer->load();
		$customer->setFvetsAllinStatus('I');
		$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
		$customersAlterados ++;
	}
	echo "WebsiteId: " . $websiteId . " <> Customers Alterados:" . $customersAlterados . "\n";
}