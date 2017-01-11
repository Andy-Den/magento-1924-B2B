<?php
require_once './configScript.php';

$promos = array('170822', '170819', '170816', '170812', '171707', '171710');

$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToFilter('website_id', $websiteId);


foreach ($promos as $promo)
{
	echo "|";
	$deleteCondition = $writeConnection->quoteInto('salesrule_id=?', $promo);
	$writeConnection->delete('fvets_salesrule_customer', $deleteCondition);

	$values = array();

	foreach ($customers as $customer)
	{
		$values[] = "('".$promo."','".$customer->getId()."','0')";
		echo "+";
	}

	$writeConnection->query("INSERT INTO fvets_salesrule_customer (salesrule_id, customer_id, position) VALUES " . implode(' , ', $values));
	echo "\n";
}