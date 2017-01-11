<?php
require_once './configScript.php';
require_once './helper.php';

$websites = Mage::getModel('core/website')->getCollection();

$finalString = 'website;id_erp;razao_social;ativo;aprovado;origem' . "\n";

foreach ($websites as $website) {

	if ($website->getId() == 1) {
		continue;
	}

	$customers = Mage::getModel('customer/customer')->getCollection()
		->addFieldToFilter('website_id', $website->getId())
		->addAttributeToSelect('name')
		->addAttributeToSelect('razao_social')
		->addAttributeToSelect('fvets_salesrep')
		->addAttributeToSelect('origem')
		->addAttributeToSelect('id_erp');

	foreach ($customers as $customer) {
		$finalString = $finalString . ($website->getName() . ";" . $customer->getIdErp() . ";" . $customer->getRazaoSocial() . ";" . $customer->getIsActive() . ";" . $customer->getData('mp_cc_is_approved') . ";" . $customer->getOrigem() . "\n");
	}
}

toFile('./extras/', 'customers', '.csv', $finalString);