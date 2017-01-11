<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/13/15
 * Time: 6:02 PM
 */
require_once './configScript.php';

$customers = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToFilter('website_id', $websiteId);
foreach ($customers as $customer)
{
	$customer->setOrigem('SITE');
	$customer->getResource()->saveAttribute($customer, 'origem');
}

$customers = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToFilter('website_id', $websiteId)
	->addAttributeToFilter('id_erp', array('gteq' => 1740));

foreach ($customers as $customer)
{
	$customer->setOrigem('SITE');
	$customer->getResource()->saveAttribute($customer, 'origem');
}

$customers = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToFilter('website_id', $websiteId)
	->addAttributeToFilter('id_erp', array('lteq' => 1740));

foreach ($customers as $customer)
{
	$customer->setOrigem('ERP');
	$customer->getResource()->saveAttribute($customer, 'origem');
}