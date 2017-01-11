<?php

require_once '../../../app/Mage.php';
umask(0);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

Mage::app()->setCurrentStore(8);


$websiteId = 5;

$lines = file("extras/forgotpassword_07.10.2014.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
	echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
	foreach ($lines as $key => $value) {
		$email = trim(explode(',', $value)[0], '"');
		$token = trim(explode(',', $value)[1], '"');
		$token = end(explode('=', $token));


		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId($websiteId);
		$customer = $customer->loadByEmail($email);

		echo explode(',', $value)[0] . '|' . explode(',', $value)[1] . '|';

		if ($token === $customer->getRpToken()) {
			//echo 'Customer never logged: ' . $customer->getEmail();
			echo '"0"';
		}
		else
		{
			echo '"1"';
		}

		echo "\n";
	}
}