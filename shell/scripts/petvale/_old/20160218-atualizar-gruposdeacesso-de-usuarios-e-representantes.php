<?php
/**
 * Created by PhpStorm.
 * User: ianitsky
 * Date: 2/18/16
 * Time: 5:37 PM
 */
require_once './configScript.php';

$reps = array(
	9 => array(183, '11,12,13,21'),
	12 => array(173, '11,12,13,21'),
	13 => array(176, '11,12,13,21'),
	14 => array(174, '12,21'),
	16 => array(180, '14'),
	22 => array(178, '12,21'),
	24 => array(195, '12,21'),
	25 => array(182, '12,21'),
	29 => array(177, '11,12,13,21'),
	30 => array(179, '11,12,13,21'),
);

echo "Atualizando representantes\n";

foreach ($reps as $rep)
{
	$writeConnection->query("UPDATE fvets_salesrep SET store_id = '{$rep[1]}' WHERE id = {$rep[0]}");
	echo '+';
}

echo "\nAtualizando customers\n";

$array = array();

if (($handle = fopen("extras/20160218-atualizar-gruposdeacesso-de-usuarios-e-representantes.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$customer = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('id_erp', $data[0])
			->addAttributeToFilter('website_id', $websiteId)
			->getFirstItem()
		;

		if (!$customer->getId())
		{
			continue;
		}

		$customer->setFvetsSalesrep($reps[$data[2]][0]);
		$customer->getResource()->saveAttribute($customer, 'fvets_salesrep');

		$customer->setStoreView($data[3]);
		$customer->getResource()->saveAttribute($customer, 'store_view');

		$customer = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToSelect('fvets_salesrep')
			->addAttributeToSelect('store_view')
			->addAttributeToFilter('id_erp', $data[0])
			->addAttributeToFilter('website_id', $websiteId)
			->getFirstItem()
		;

		$array[] = $customer->getData();

		echo '+';
	}
	fclose($handle);
}

print_r($array);