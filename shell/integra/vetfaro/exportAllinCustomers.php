<?php
require_once __DIR__ . '/configIntegra.php';

$website = Mage::getModel('core/website')->load($websiteId);
$distName = $website->getName();

$channel = 'integration';
$errorChannel = 'integration';
Mage::helper('datavalidate')->sendSlackMessage($channel, "[All In - Início Integração] Iniciando integração da $distName");

try {
	$helper = Mage::helper('fvets_allin');
	$accounts = Mage::getModel('fvets_allin/account')
		->getCollection()
		->addFieldToFilter('website_id', $websiteId);

	foreach ($accounts as $account) {
		$customers = $helper->getAllCustomersList($account->getId());
		$customerControll = Mage::getModel('fvets_allin/customers');

		$allRowsValues = $customerControll->massRemoteUpdate($account->getId(), $customers, 'V4');

		if (Mage::getConfig('allin/general/data_log_enabled')) {
			$helper->saveDataLog($account->getWebsiteId(), $allRowsValues);
		}
	}
} catch (Exception $ex) {
	Mage::helper('datavalidate')->sendSlackMessage($errorChannel, "[All In - Erro na integracao da $distName] Erro: (" . $ex->__toString() . ")");
	Mage::throwException($ex->__toString());
}
Mage::helper('datavalidate')->sendSlackMessage($channel, "[All In - Fim Integração] Integração da $distName finalizada");