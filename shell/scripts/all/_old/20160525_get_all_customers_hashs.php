<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/25/16
 * Time: 10:46 AM
 */

require_once './configScript.php';
$websitesId = array(15, 12, 11, 9, 10);
$print = '';

foreach ($websitesId as $websiteId) {
	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $websiteId)
		->addAttributeToSelect('password_hash');

	foreach ($customers as $customer) {
		$print .= $customer->getId() . "|" . $customer->getPasswordHash() . "\n";
	}
}

printFile('clientes_hashes', $print);

function printFile($fileName, $data)
{
	global $websiteId;
	$myfile = fopen('extras/' . date('Ymd') . "_" . $fileName . ".csv", "w") or die("Unable to open file!");
	fwrite($myfile, $data);
	fclose($myfile);
}