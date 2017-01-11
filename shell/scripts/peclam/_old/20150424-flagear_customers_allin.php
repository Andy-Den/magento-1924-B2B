<?php

require_once '../../../app/Mage.php';
umask(0);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

Mage::app()->setCurrentStore(0);

$websiteId = 2;

$lines = file("extras/2015-04-24_peclam_allin.txt", FILE_IGNORE_NEW_LINES);
$errors = array();
$clientesAtualizados = array();
$clientesNaoEncontrados = array();
$clientesRepetidos = array();

$jaFlageados = array();

if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	foreach ($lines as $email) {
		echo '+';
		try {
			$customer = Mage::getModel('customer/customer');
			$customer->setWebsiteId($websiteId);
			$customer->loadByEmail($email);
			if ($customer->getId()) {
				if (!in_array($email, $jaFlageados)) {
					$customer->setFvetsAllinStatus('V');
					$customer->getResource()->saveAttribute($customer, 'fvets_allin_status');
					$jaFlageados[] = $email;
					$clientesAtualizados[] = $email;
				} else {
					$clientesRepetidos[] = $email;
				}
			} else {
				$clientesNaoEncontrados[] = $email;
			}
		} catch (Exception $e) {
			$errors[] = $email . ' (' . (string)$e->getMessage() . ')';
		}
	}

	echo "\n";
	echo count($clientesAtualizados) . ' - Clientes atualizados' . "\n";
	echo count($clientesNaoEncontrados) . ' - Clientes não encontrados' . "\n";
	echo count($clientesRepetidos) . ' - Clientes repetidos' . "\n";
	echo count($errors) . ' - Erros ao salvar clientes' . "\n";
	echo "\n";
	echo "< Clientes não encontrados >\n";
	foreach($clientesNaoEncontrados as $cliente) {
		echo $cliente . "\n";
	}
	echo "\n< Clientes repetidos >\n";
	foreach($clientesRepetidos as $cliente) {
		echo $cliente . "\n";
	}
}