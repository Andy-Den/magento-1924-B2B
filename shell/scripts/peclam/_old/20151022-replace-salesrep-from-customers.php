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

$replacedIds = array(198 => 219); // Ids da 4Vets

$string = '"ID", "ID ERP", "NOME", "EMAIL"' . "\n";

$count = 0;

foreach ($replacedIds as $key => $replacedId)
{
	$customers = Mage::getModel('customer/customer')->getCollection();
	$customers
		->addFieldToFilter('website_id', $websiteId)
		->addAttributeToSelect('id_erp')
		->addAttributeToSelect('firstname')
		->addAttributeToSelect('lastname')
		->getSelect()->join(array('cev' => 'customer_entity_varchar'), "cev.entity_id = e.entity_id and cev.attribute_id = 148 and cev.entity_type_id = 1 and find_in_set($key, cev.value)", 'cev.value as fvets_salesrep');

	//echo $customers->getSelect()->__toString();

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
		$count++;

		$string .= '"' . implode ('","', array($customer->getId(), $customer->getIdErp(), $customer->getFirstname() . ' ' . $customer->getLastname(), $customer->getEmail())) . '"' . "\n";
	}

	echo "\n";
}

$filename = 'extras' . DS . date(Ymd) . '-replace-salesrep-from-customers.csv';
$channel = 'scripts';

file_put_contents($filename, $string);

Mage::helper('datavalidate')->createChannel($channel);

Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), $count . " - Clientes movidos do representante Clemilson (id 2171) para Anderson Rosa Tosta (id 2387)", Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'peclam' . DS . $filename, 'csv', $channel);

