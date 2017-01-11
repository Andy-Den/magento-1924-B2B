<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/9/15
 * Time: 10:19 AM
 */

require_once './configScript.php';

$collection  = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToSelect('entity_id')
	->addAttributeToSelect('website_id')
	->addAttributeToSelect('firstname')
	->addAttributeToSelect('lastname')
	->addAttributeToSelect('email')
	->addAttributeToSelect('default_billing')
	/*->addAttributeToFilter(
		array(
			array('attribute' => 'default_billing', 'null' => '', 'left'),
			array('attribute' => 'default_shipping', 'null' => '', 'left')
		),
		'left'
	)*/
	->addAttributeToFilter('default_shipping', array('null' => ''), 'left')
	->addAttributeToFilter('mp_cc_is_approved', array('eq' => '1'))
	->addAttributeToFilter('website_id', array('neq' => '1'))
;


foreach ($collection as $customer)
{
	$customer->setDefaultShipping($customer->getDefaultBilling());
	$customer->getResource()->saveAttribute($customer, 'default_shipping');
}

echo "\n";

$channel = 'scripts';
//$channel = 'test';

Mage::helper('datavalidate')->createChannel($channel);

$filename = date('Ymd').'-default_shipping.csv';
$data = $finalData;

if (file_put_contents('extras'. DS .$filename, $data))
{
	Mage::helper('datavalidate')->sendSlackFile("Attributo default_shipping configurado para clientes", Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'all' . DS . 'extras' . DS . $filename, 'csv', $channel);
}
else
{
	echo "Arquivo" . $filename . " não salvo" . "\n";
}

echo "\n";
echo 'Bye';