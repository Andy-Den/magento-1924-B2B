<?php
require_once './configScript.php';

$customers = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToFilter('website_id', $websiteId)
	->addAttributeToSelect('id_erp')
	->addAttributeToSelect('razao_social');
foreach ($customers as $customer) {
	if ($customer->getIdErp() && strlen($customer->getIdErp()) < 6) {
		echo $customer->getIdErp() . "|" . $customer->getRazaoSocial() . "\n";
	} else {
		//echo "-";
	}
}