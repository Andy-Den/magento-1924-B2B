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
$websiteCode = 'omega';
//end

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$website = Mage::getModel('core/website')->load($websiteCode);
//gera tokens de alteração de senha para os usuários
$customers = Mage::getModel('customer/customer')->getCollection()
	//->addFieldToFilter('email', 'ti+loja@4vets.com.br')
	->addFieldToFilter('website_id', $website->getId());

$result = '';

foreach ($customers as $customer) {
	$newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
	$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);

	$customer = Mage::getModel('customer/customer')->load($customer->getId());

	$customerStoreviews = explode(',', $customer->getStoreView())[0];

	$name = $customer->getName();
	if (trim($name) == '')
	{
		$name = $customer->getRazaoSocial();
	}

	$result = $result . ($name . '|' . $customer->getEmail() . '|' . Mage::getModel('core/store')->load($customerStoreviews)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . 'activate?id=' . $customer->getId() . '&token=' . $customer->getRpToken() . "\n");
}

$myfile = fopen("extras/customers_activate_link.csv", "w") or die("Unable to open file!");
fwrite($myfile, $result);
fclose($myfile);

echo $result;