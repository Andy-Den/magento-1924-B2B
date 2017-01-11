<?php
require_once '/wwwroot/current/shell/integra/classic/configIntegra.php';
require_once __DIR__ . '/../../scripts/_functions/exportAllinCustomers.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/15/15
 * Time: 6:23 PM
 */
$distName = 'Classic';

$channel = 'integration';
$errorChannel = 'integration';
Mage::helper('datavalidate')->sendSlackMessage($channel, "[All In - Início Integração] Iniciando integração do $distName");

try
{
	$helper = Mage::helper('fvets_allin');
	$accounts = Mage::getModel('fvets_allin/account')
		->getCollection()
		->addFieldToFilter('website_id', $websiteId);

	foreach ($accounts as $account)
	{
		$customerFilters = array();
		$customerFilters['website_id'] = array('eq' => $websiteId);

		$customers = $helper->getAllCustomersList($account->getId(), $customerFilters);
		$customerControll = Mage::getModel('fvets_allin/customers');

		$allRowsValues = $customerControll->massRemoteUpdate($account->getId(), $customers, 'Classic');

		if (Mage::getConfig('allin/general/data_log_enabled'))
		{
			$helper->saveDataLog($account->getWebsiteId(), $allRowsValues);
		}
	}
} catch (Exception $ex)
{
	Mage::helper('datavalidate')->sendSlackMessage($errorChannel, "[All In - Erro na integracao do $distName] Erro: (" . $ex->__toString() . ")");
	Mage::throwException($ex->__toString());
}
Mage::helper('datavalidate')->sendSlackMessage($channel, "[All In - Fim Integração] Integração do $distName finalizada");