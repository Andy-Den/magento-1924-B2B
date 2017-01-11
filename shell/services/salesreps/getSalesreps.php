<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 2/2/16
 * Time: 10:48 AM
 */

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

array_shift($argv);
list($websiteId) = $argv;

//$websiteId = 5;

if (empty($websiteId))
{
	die('You must provide a website id.');
}

$stores = Mage::getModel('core/website')->load($websiteId)->getStoreIds();

$storesFilterArray = array();
foreach($stores as $store) {
	$storesFilterArray[] = array('finset' => $store);
}
$salesreps = Mage::getModel('fvets_salesrep/salesrep')->getCollection()
	->addFieldToFilter('store_id',
		$storesFilterArray
		)
	->addFieldToFilter('status', 1)
	->setOrder('name', 'asc');

//die($salesreps->getSelect()->__toString());

$data = array();
foreach ($salesreps as $salesrep)
{
	$data[] = array('value' => $salesrep->getId(),
		'label' => $salesrep->getName());
}
echo json_encode($data);
