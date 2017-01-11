<?php
require_once '/wwwroot/current/shell/scripts/datavalidate/config.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/6/15
 * Time: 6:44 PM
 */

$stringReturn = '';

try
{
	foreach ($websitesId as $websiteId)
	{
		$websiteCode = getWebsiteCode($websiteId);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId);

		$contadorTotal = count($customerCollection);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addFieldToFilter('origem', 'ERP');

		$contadorOrigemErp = count($customerCollection);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addFieldToFilter('origem', 'SITE');

		$contadorOrigemSite = count($customerCollection);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId);
		$customerCollection->getSelect()
			->joinleft(array('cev' => 'customer_entity_varchar'), "cev.entity_id = e.entity_id and cev.attribute_id = (select attribute_id from eav_attribute where attribute_code = 'origem')")
			->where('cev.value is null');
		$contadorNulos = count($customerCollection);

		$stringReturn .= 'Webside ID: ' . $websiteId . "\n<br>";
		$stringReturn .= 'Customers Total: ' . $contadorTotal . "\n<br>";
		$stringReturn .= 'Customers Origem ERP: ' . $contadorOrigemErp . "\n<br>";
		$stringReturn .= 'Customers Origem SITE: ' . $contadorOrigemSite . "\n<br>";
		$stringReturn .= 'Customers Nulos: ' . $contadorNulos . "\n<br>";
	}

	Mage::helper('datavalidate')->sendSlackMessage('datavalidate', "[Customers - Origem] Script para verificação do status dos dados de origem dos customers \n\n" . $stringReturn);
} catch (Exception $ex)
{
	//let the flow continues
}
