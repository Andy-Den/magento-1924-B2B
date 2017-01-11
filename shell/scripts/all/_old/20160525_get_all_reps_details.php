<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/25/16
 * Time: 3:48 PM
 */
require_once './configScript.php';

$websites = Mage::getModel('core/website')->getCollection();
foreach ($websites as $website) {

	$salesreps = Mage::getModel('fvets_salesrep/salesrep')->getCollection()
		->addStoresToFilter($website->getStoreIds())
		->addFieldToFilter('status', 1);

	$salesrepInfo = '';

	foreach ($salesreps as $salesrep) {
		$salesrep->load();
		$salesrepInfo .= $salesrep->getName() . "|" . $salesrep->getEmail() . "|" . $salesrep->getIdErp() . "|" . $salesrep->getTelephone() . "|" . getFormatedRepBrands($salesrep) . "\n";
	}

	echo "[[ Reps do(a) " . $website->getName() . "]]\n";
	echo $salesrepInfo;
}

function getFormatedRepBrands($salesrep)
{
	$categories = $salesrep->getSelectedCategories(false);
	$result = '';
	foreach ($categories as $category) {
		$result .= $category->getName() . "|";
	}

	$result = rtrim($result, '|');

	return $result;
}