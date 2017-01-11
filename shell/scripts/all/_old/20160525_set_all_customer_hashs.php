<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/25/16
 * Time: 11:27 AM
 */
require_once './configScript.php';

$lines = file("extras/20160525_clientes_hashes.csv", FILE_IGNORE_NEW_LINES);

$fileArray = array();
if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
	exit;
} else {
	foreach ($lines as $line) {
		$customerId = explode('|', $line)[0];
		$hash = explode('|', $line)[1];

		if (!$customerId) {
			continue;
		}
		$customer = Mage::getModel('customer/customer')->load($customerId);

		if ($customer && $customer->getId()) {
			if ($customer->getPasswordHash() != $hash) {
				$customer->setPasswordHash($hash);
				//$customer->getResource()->saveAttribute($customer, 'password_hash');
				echo "Atualizando password de: " . $customer->getId() . "\n";
			}
		}
	}
}