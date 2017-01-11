<?php
require_once './configScript.php';

$storeIds = Mage::getModel('core/website')->load($websiteId)->getStoreIds();
$salesreps = Mage::getModel('fvets_salesrep/salesrep')->getCollection();

$arrayTemp = array();
foreach ($storeIds as $storeId) {
	$arrayTemp[] = array('finset' => $storeId);
}

$salesreps->addFieldToFilter('store_id', $arrayTemp);

$result = '';
foreach ($salesreps as $salesrep) {
	$salesrep->load();
	$customers = $salesrep->getSelectedCustomersCollection();
	$result .= "[REP]" . "|" . $salesrep->getName() . "\n";
	foreach ($customers as $customer) {
		$customer->load();
		if ($customer->getFvetsAllinStatus() == 'V') {
			$result .= $customer->getIdErp() . "|" . $customer->getName() . "\n";
		}
	}
	echo "+";
}

$myfile = fopen('extras/' . date('Ymd') . "_" . $websiteId . ".csv", "w") or die("Unable to open file!");
fwrite($myfile, $result);
fclose($myfile);