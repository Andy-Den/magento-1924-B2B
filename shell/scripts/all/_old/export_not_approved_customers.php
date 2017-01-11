<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/23/15
 * Time: 1:21 PM
 */

require_once './configScript.php';

foreach ($websitesId as $websiteId)
{
	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $websiteId)
		->addAttributeToFilter('mp_cc_is_approved', 0)
		->addFieldToFilter('email', array('nlike' => '%@4vets.com.br'));
	foreach ($customers as $customer)
	{
		$customer->load();
		echo($customer->getName() . ',' . $customer->getRazaoSocial() . ',' . $customer->getEmail() . ',' . $customer->getTelefone() . "\n");
	}
}