<?php
require_once './configIntegra.php';

$lines = file("$testDirectoryImport/customersxreps-doctorsvet.csv", FILE_IGNORE_NEW_LINES);
$repDefault = 4;
$storeViews = '8,9,10';

if (empty($lines)) {
	echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
	$query = 'delete cev FROM customer_entity_varchar cev
WHERE cev.entity_id IN (
SELECT ce.entity_id
FROM customer_entity ce
WHERE ce.website_id = 5) AND cev.attribute_id = (
SELECT ea.attribute_id
FROM eav_attribute ea
WHERE ea.attribute_code = \'fvets_salesrep\' AND ea.entity_type_id = 1)';

	$writeConnection->query($query);

	foreach ($lines as $key => $value) {
		$line = str_getcsv($value, '|', "'");
		$email = trim($line[0]);
		$idErp = $line[1];
		echo $email;
		echo "\n";
		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId(5);
		$customer = $customer->loadByEmail($email);
		$customer->setData('fvets_salesrep', getRepByIdErp($idErp, $storeViews)->getId());
		if ($customer->getId()) {
			$customer->getResource()->saveAttribute($customer, 'fvets_salesrep');
		}
	}

	$query = 'SELECT ce.email
FROM customer_entity ce
LEFT JOIN customer_entity_varchar cev ON cev.entity_id = ce.entity_id AND cev.attribute_id = (
SELECT ea.attribute_id
FROM eav_attribute ea
WHERE ea.attribute_code = \'fvets_salesrep\' AND ea.entity_type_id = 1)
WHERE ce.website_id = 5 and value is null';

	$customerEmails = $readConnection->fetchAll($query);
	$repDefault = getRepByIdErp($repDefault, $storeViews);

	foreach ($customerEmails as $email) {
		echo $email['email'];
		echo "\n";
		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId(5);
		$customer = $customer->loadByEmail($email['email']);
		$customer->setData('fvets_salesrep', $repDefault->getId());
		if ($customer->getId()) {
			$customer->getResource()->saveAttribute($customer, 'fvets_salesrep');
		}
	}
}

function getRepByIdErp($repId, $store_views) {
	$reps = Mage::getModel('fvets_salesrep/salesrep')->getCollection()
		->addFieldToFilter('id_erp', $repId)
		->addFieldToFilter('store_id', array('eq', $store_views));
	$rep = null;

	foreach($reps as $_rep) {
		$rep = $_rep;
		break;
	}
	return $rep;
}
