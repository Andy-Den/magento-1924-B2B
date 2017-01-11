<?php
/**
 * Created by PhpStorm.
 * User: douglas
 * Date: 5/19/15
 * Time: 09:43 AM
 */
require_once './configScript.php';


$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToFilter('website_id', $websiteId)
	;

$changedPassword = array();
$notChangedPassword = array();
foreach ($customers as $customer) {

	$logCustomer = Mage::getModel('log/customer')->loadByCustomer($customer->getId());


	if ($logCustomer->getLoginAt() === null)
	{
		$customer->changePassword('omega123');
		$customer->save();

		$changedPassword[] = implode(',', $customer->getData());

		echo '+';
	}
	else
	{
		$notChangedPassword[] = implode(',', $customer->getData());

		echo '-';
	}


}

echo "\n";

$channel = 'scripts';

Mage::helper('datavalidate')->createChannel($channel);

echo count($changedPassword) . " - Clientes alterados" . "\n";

$filename = date('Ymd').'-changedPassword.csv';
$data = implode("\n", $changedPassword);

if (file_put_contents('extras'. DS .$filename, $data))
{
	Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), count($changedPassword) . " - Clientes alterados", Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'omega' . DS . 'extras' . DS . $filename, 'csv', $channel);
}
else
{
	echo "Arquivo" . $filename . " não salvo" . "\n";
}

echo count($notChangedPassword) . " - Clientes não alterados" . "\n";

$filename = date('Ymd').'-notChangedPassword.csv';
$data = implode("\n", $notChangedPassword);

if (file_put_contents('extras'. DS .$filename, $data))
{
	Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), count($notChangedPassword) . " - Clientes não alterados", Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'omega' . DS . 'extras' . DS . $filename, 'csv', $channel);
}
else
{
	echo "Arquivo" . $filename . " não salvo" . "\n";
}

