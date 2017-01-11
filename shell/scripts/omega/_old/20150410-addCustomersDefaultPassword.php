<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 1/29/15
 * Time: 10:43 AM
 */

require_once '../../../app/Mage.php';

umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);
umask(0);

//local variables
$websiteCode = 'omega';
//end

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

$customers = Mage::getModel('customer/customer')->getCollection()
				->addAttributeToFilter('website_id',4)
				->addAttributeToFilter('entity_id', array('nin' => '4012,4011,4010,4009,4008,4007,4006,4005,4004,4003,4002,1588'))
;

$changed = '';
foreach ($customers as $customer) {
	$customer = Mage::getModel('customer/customer')->load($customer->getId());
	$customer->setPassword('omega123');
	$customer->save();
	$changed .= implode('|', $customer->getData())."\n";
	echo '+';
}

echo "\n" . $customers->count() . ' clientes alterados!';

file_put_contents('extras/'.date('Ymd').'-addCustomerDefaultPassword.csv', $changed);