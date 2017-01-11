<?php
require_once '/wwwroot/current/shell/scripts/datavalidate/config.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/6/15
 * Time: 6:44 PM
 */

$stringReturn = '';
$toEmail = 'julio@4vets.com.br';
$toName = 'Júlio Reis';
$subject = 'Relatório Status Customers Allin';

$scriptName = "[All In]";

try {
	foreach ($websitesId as $websiteId) {
		$websiteCode = getWebsiteCode($websiteId);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addFieldToFilter('mp_cc_is_approved', 1)
			->addAttributeToFilter('fvets_allin_status', 'V');

		$contadorValidos = count($customerCollection);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addFieldToFilter('mp_cc_is_approved', 1)
			->addAttributeToFilter('fvets_allin_status', 'I');

		$contadorInvalidos = count($customerCollection);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addFieldToFilter('mp_cc_is_approved', 1);

		$customerCollection->getSelect()
			->joinleft(array('cev' => 'customer_entity_varchar'), "cev.entity_id = e.entity_id and cev.attribute_id = (select attribute_id from eav_attribute where attribute_code = 'fvets_allin_status')")
			->where('cev.value is null');

		$contadorSemFlag = count($customerCollection);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addFieldToFilter('mp_cc_is_approved', 1);

		$contadorAprovados = count($customerCollection);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addFieldToFilter('mp_cc_is_approved', 0);

		$contadorNaoAprovados = count($customerCollection);

		$customerCollection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addFieldToFilter('mp_cc_is_approved', 1)
			->addAttributeToFilter('second_email', array('neq' => ''));

		$contadorPossuemSecondEmail = count($customerCollection);

		$stringReturn .= 'Webside ID: ' . $websiteId . "\n<br>";
		$stringReturn .= 'Customers Aprovados: ' . $contadorAprovados . "\n<br>";
		$stringReturn .= 'Customers Não Aprovados: ' . $contadorNaoAprovados . "\n<br>";
		$stringReturn .= 'Customers Aprovados com Status Allin Válido: ' . $contadorValidos . "\n<br>";
		$stringReturn .= 'Customers Aprovados com Status Allin Inválido: ' . $contadorInvalidos . "\n<br>";
		$stringReturn .= 'Customers Aprovados com Status Allin Sem Flag: ' . $contadorSemFlag . "\n<br>";
		$stringReturn .= 'Customers que possuem "second email": ' . $contadorPossuemSecondEmail . "\n\n\n<br><br><br>";
	}
	//sendMail($toEmail, $toName, $subject, $stringReturn);

	Mage::helper('datavalidate')->sendSlackMessage('datavalidate', $scriptName . " <". $_SERVER['PHP_SELF'] . ">\n\n" . $stringReturn);
} catch (Exception $ex) {
	try {
		sendMail($toEmail, $toName, $subject, $ex->getMessage());
	} catch (Exception $ex) {
		//let the flow continues
	}
}