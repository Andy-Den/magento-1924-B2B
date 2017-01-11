<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 7/28/15
 * Time: 11:15 AM
 */
require_once './configScript.php';

$customers = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToFilter('website_id', $websiteId)
->addAttributeToFilter('fvets_allin_status', 'V');

foreach ($customers as $customer)
{
	$customer->setData('fvets_allin_status', 'I');
	$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
}