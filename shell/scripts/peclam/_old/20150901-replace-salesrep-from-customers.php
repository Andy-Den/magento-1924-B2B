<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 8/28/15
 * Time: 12:12 PM
 */

require_once '../../../app/Mage.php';
umask(0);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

Mage::app()->setCurrentStore(2);

$websiteId = 2;

//Natan -> Kainan
$replacedIdsErp = array(196 => 215);

foreach ($replacedIdsErp as $key => $replacedId)
{
	$customers = Mage::getModel('customer/customer')->getCollection();
	$customers
		->addFieldToFilter('website_id', $websiteId)
		->getSelect()->join(array('cev' => 'customer_entity_varchar'), "cev.entity_id = e.entity_id and cev.attribute_id = 148 and cev.entity_type_id = 1 and find_in_set($key, cev.value)", 'cev.value as fvets_salesrep');

	//echo $customers->__toString();

	foreach ($customers as $customer)
	{
		$fvetsSalesreps = $customer->getFvetsSalesrep();

		$fvetsSalesreps = explode(',', $fvetsSalesreps);

		foreach ($fvetsSalesreps as $ind => $fvetsSalesrep)
		{
			if ($fvetsSalesrep == $key)
			{
				$fvetsSalesreps[$ind] = $replacedId;
				break;
			}
		}

		$customer->setFvetsSalesrep(implode(',', $fvetsSalesreps));
		$customer->getResource()->saveAttribute($customer, 'fvets_salesrep');

		echo "+";
	}

	echo "\n";
}