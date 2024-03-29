<?php
require_once './configScript.php';
require_once '../_functions/exportAllinCustomers.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/15/15
 * Time: 6:23 PM
 */

$website = Mage::getModel('core/website')->load($websiteId);
$customerControll = Mage::getModel('fvets_allin/customers');

$lista = array(
	'nm_lista' => $website->getCode() . '_integravet_' . 'v4',
	'campos' => $customerControll->camposLayoutV4['campos']);

try {
	$helper = Mage::helper('fvets_allin');
	$accounts = Mage::getModel('fvets_allin/account')
		->getCollection()
		->addFieldToFilter('website_id', $websiteId);

	foreach ($accounts as $account) {
		$customerControll->createListIfNotExists($account->getId(), $lista);
	}
} catch (Exception $ex) {
	Mage::throwException($ex->__toString());
}