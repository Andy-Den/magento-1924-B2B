<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 2/5/16
 * Time: 3:27 PM
 */

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

array_shift($argv);
list($fromSalesrepId, $toSalesrepId, $websiteId) = $argv;

$replacedIdsErp = array($fromSalesrepId => $toSalesrepId);

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

		//echo "+";
	}
	//echo "\n";
}
echo '200';
