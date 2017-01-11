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

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

$websiteId = 2;

$replacedIdsErp = array(16 => 215, 6 => 215, 8 => 217, 20 => 217);

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

$query = "SELECT ce.entity_id, ce.email, fs.name, fs.id_erp
FROM customer_entity ce
left JOIN customer_entity_varchar cev ON cev.entity_type_id = 1 AND cev.attribute_id = 148 AND cev.entity_id = ce.entity_id
join fvets_salesrep fs on fs.id = cev.value
WHERE ce.website_id = 2 and fs.id_erp not in (2,6,9964,1540,1645,1726,2168,2170,2171,2244)";

$customersWithWrongRep = $readConnection->fetchAll($query);

foreach($customersWithWrongRep as $customer) {
	echo ($customer['entity_id'] . "|" . $customer['email'] . "|" . $customer['name'] . "|" . $customer['id_erp'] . "\n");
}