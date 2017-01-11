<?php
require_once './configScript.php';

$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToFilter('website_id', $websiteId);
echo "Total de customers: " . count($customers) . "\n";
$count = 0;
$rel = array();
foreach ($customers as $customer) {
	$customer->load();
	$restrictionGroups = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup')->getCollection()
		->addCustomerFilter($customer);

	if ($restrictionGroups && count($restrictionGroups) > 0) {
		$count++;
		$arrayTemp = array();
		foreach ($restrictionGroups as $restrictionGroup) {
			$arrayTemp[] = $restrictionGroup->getName();
		}
		$rel[$customer->getId() . ',' . $customer->getName()] = $arrayTemp;
	} else {
		$rel[$customer->getId() . ',' . $customer->getName()] = null;
	}
}
echo "Total de customers com restrição: " . $count . "\n";

echo "Cód. Cliente,Nome do Cliente,Grupo de restrição 1,Grupo de restrição 2,Grupo de restrição 3\n";
foreach ($rel as $key => $item) {
	echo $key . "," . implode(',', $item) . "\n";
}