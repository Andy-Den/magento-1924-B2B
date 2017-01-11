<?php

require_once '../../../app/Mage.php';
umask(0);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

Mage::app()->setCurrentStore(2);

$websiteId = 2;

// Procurar todos os customers que já compraram

/**
 * Now get all unique customers from the orders of these items.
 */
$orderCollection = Mage::getResourceModel('sales/order_collection')
	->addFieldToFilter('customer_id', array('neq' => 'NULL'));
$orderCollection->getSelect()->group('customer_id');

$logCollection = Mage::getResourceModel('log/visitor_collection');
$logCollection->getSelect()
	->join(
		array('log_customer'=> $logCollection->getTable('log/customer')),
		'main_table.`visitor_id`= `log_customer`.`visitor_id`'
	)
	->reset(Zend_Db_Select::COLUMNS)
	->columns('log_customer.customer_id')
	->group('customer_id')
	;

$loggedCustomers = array_merge($orderCollection->getColumnValues('customer_id'), $logCollection->getColumnValues('customer_id'));

/**
 * Now get a customer collection for not those customers.
 */
$customerCollection = Mage::getModel('customer/customer')->getCollection()
	->addFieldToFilter('entity_id', array('nin' => $loggedCustomers))
	->addFieldToFilter('website_id', $websiteId);


$changedPassword = array();
$error = array();

foreach ($customerCollection as $customer)
{
	echo '+';

	try {
		$customer->changePassword('peclam123');
		$customer->save();

		$changedPassword[] = implode(',', $customer->getData());

	} catch (Exception $e)	{
		$error[] = (string)$e->getMessage() . implode(',', $customer->getData());
	}
}

$string = 'Senhas alteradas para clientes: ' . "\n\n";
foreach ($changedPassword as $change) {
	$string .= $change."\n";
}

$string .= "\n\n\n\n";

$string .= 'Erro ao alterar senhas de clientes: ' . "\n\n";
foreach ($errors as $error) {
	$string .= $error;
}

file_put_contents('extras/'.date('dmY').'-senhas-alteradas.csv', $string);
echo "\n";
echo count($changedPassword) . ' - Senhas alteradas' . "\n";
echo count($error) . ' - Senhas não alteradas' . "\n";