<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 2/1/16
 * Time: 3:21 PM
 */

require_once './configScript.php';

$customers = Mage::getModel('customer/customer')->getCollection();
$customers
	->addFieldToFilter('website_id', $websiteId);

foreach ($customers as $customer)
{
	if ($customer->getGroupId() != 29)
	{
		if ($customer && $customer->getId())
		{
			$customer->setGroupId(29);
			$customer->save();
			echo "+";
		}
	}
}
