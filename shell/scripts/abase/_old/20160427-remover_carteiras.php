<?php
require_once './configScript.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/20/16
 * Time: 5:30 PM
 */

//$customers = Mage::getModel('customer/customer')->getCollection()
//	->addAttributeToFilter('website_id', $websiteId)
//	->addAttributeToFilter('fvets_salesrep', array(
//		array('finset' => '322'),
//		array('finset' => '323'),
//		array('finset' => '324')
//	));
//
//foreach ($customers as $customer) {
//	$customer->load();
//	if ($customer->getFvetsSalesrep()) {
//		echo $customer->getFvetsSalesrep() . "\n";
//	}
//}
//echo "Total: " . count($customers) . "\n";



//==========================================

//carteiras a serem removidas - Entity ID dos reps
$salesrepsIds = array(322, 323, 324);

foreach ($salesrepsIds as $salesrepsId) {
	$salesrep = Mage::getModel('fvets_salesrep/salesrep')->load($salesrepsId);
	$customers = $salesrep->getSelectedCustomersCollection();
	foreach ($customers as $customer) {
		$customer->setIsActive(0);
		$customer->setData('fvets_allin_status', 'I');
		$customer->setData('block_allin_status', 1);
		$customer->save();
		echo $customer->getId() . "\n";
	}
}