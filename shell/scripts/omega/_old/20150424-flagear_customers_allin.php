<?php

require_once '../../../app/Mage.php';
umask(0);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

Mage::app()->setCurrentStore(0);

$websiteId = 4;

$errors = array();
$clientesAtualizados = array();
$clientesNaoEncontrados = array();

$customers = Mage::getModel('customer/customer')
	->getCollection()
  ->addAttributeToFilter('website_id', $websiteId);;

foreach ($customers as $customer) {
	echo '+';
	try {
		$customer->load();
		$customer->setFvetsAllinStatus('V');
		$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
		$clientesAtualizados[] = $email;
	} catch (Exception $e) {
		$errors[] = $email . ' (' . (string)$e->getMessage() . ')';
	}
}

$string = 'Clientes atualizados:' . "\n\n\n";
foreach ($clientesAtualizados as $cliente) {
	$string .= implode(',', $cliente) . "\n";
}

$string .= "\n\n\n\n\n\n\n\n" . 'Clientes não encontrados: ' . "\n\n\n";

foreach ($clientesNaoEncontrados as $cliente) {
	$string .= implode(',', $cliente) . "\n";
}

$string .= "\n\n\n\n\n\n\n\n" . 'Erros ao tentar salvar clientes: ' . "\n\n\n";

foreach ($errors as $error) {
	$string .= (string)$error . "\n";
}

echo "\n";
echo count($clientesAtualizados) . ' - Clientes atualizados' . "\n";
echo count($clientesNaoEncontrados) . ' - Clientes não encontrados' . "\n";
echo count($errors) . ' - Erros ao salvar clientes' . "\n";