<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 1/29/15
 * Time: 10:43 AM
 */

require_once '../../../app/Mage.php';

umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);
umask(0);

//local variables
$websiteCode = 'peclam';
$emailDestino = 'julio@4vets.com.br';
//end

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$website = Mage::getModel('core/website')->load($websiteCode);
//gera tokens de alteração de senha para os usuários
$customers = Mage::getModel('customer/customer')->getCollection()
	//->addFieldToFilter('email', 'julio@4vets.com.br')
	//->addFieldToFilter('entity_id', '1632');
	->addFieldToFilter('website_id', $website->getId());

$result = '';

foreach ($customers as $customer) {
	//$newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
	//$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);

	$customer = Mage::getModel('customer/customer')->load($customer->getId());

	$repId = $customer->getData('fvets_salesrep');

	if (!isset($repId) || $repId == '') {
		continue;
	}

	$rep = Mage::getModel('fvets_salesrep/salesrep')->load($repId);

	$result = $result . ($customer->getEmail() . '|' . $rep->getName() . '|' . $rep->getEmail() . '|' . $rep->getTelephone() . '|' . 'http://4vets-whitelabel.s3.amazonaws.com/doctorsVet/transacionais/avatar-representantes/' . $rep->getId() . '.png' . "\n");
}

$myfile = fopen($websiteCode . "_customers_reps_data.csv", "w") or die("Unable to open file!");
fwrite($myfile, $result);
fclose($myfile);