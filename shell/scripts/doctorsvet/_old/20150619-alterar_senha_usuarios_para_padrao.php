<?php
/**
 * Created by PhpStorm.
 * User: douglas
 * Date: 6/19/15
 * Time: 14:40 PM
 */
require_once './configScript.php';

/**
 * Pegar os pedidos para não buscar os clientes que já compraram
 */
$orderCollection = Mage::getResourceModel('sales/order_collection')
	->addFieldToFilter('customer_id', array('neq' => 'NULL'));
$orderCollection->getSelect()->group('customer_id');

$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToSelect('password_hash')
	->addAttributeToSelect('name')
	->addAttributeToSelect('id_erp')
	->addAttributeToSelect('first_name')
	->addAttributeToSelect('last_name')
	->addAttributeToSelect('last_name')
	->addAttributeToSelect('razao_social')
	->addAttributeToSelect('created_in')
	->addAttributeToFilter('website_id', $websiteId)
	->addFieldToFilter('entity_id', array('nin' => $orderCollection->getColumnValues('customer_id')))
	;

$changedPassword = array('ID,ID ERP,E-mail,Nome,Razão Social,Loja');
$notChangedPassword = array('ID,ID ERP,E-mail,Nome,Razão Social,Loja,Último login');

var_dump($customers->count());

foreach ($customers as $customer) {


	$logCustomer = Mage::getModel('log/customer')->loadByCustomer($customer->getId());

	$customerLine = $customer->getId() . ',' . $customer->getIdErp() . ',' . $customer->getEmail() . ',' . $customer->getFirstName() . ' ' . $customer->getLastName() . ',' . $customer->getRazaoSocial() . ',' . $customer->getCreatedIn();

	if ($logCustomer->getLoginAt() === null)
	{
		$customer->changePassword('123456');
		$customer->save();

		$changedPassword[] = $customerLine;

		echo '+';
	}
	else
	{
		$notChangedPassword[] = $customerLine . ',' . $logCustomer->getLoginAt();

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
	Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), count($notChangedPassword) . " - Clientes não alterados", Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'omega' . DS . 'extras' . DS . $filename, 'csv', $channel);}
else
{
	echo "Arquivo" . $filename . " não salvo" . "\n";
}


echo 'Bye' . "\n";