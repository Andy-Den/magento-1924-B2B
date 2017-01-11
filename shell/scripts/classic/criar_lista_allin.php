<?php
require_once './configScript.php';
require_once '../_functions/exportAllinCustomers.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/15/15
 * Time: 6:23 PM
 */
$label = Mage::helper('fvets_allin')->getAttributeLabelByCodeAndValue('hive_of_activity', null);
echo $label;

exit;
$lista = array(
	'nm_lista' => 'classic_lista_integrada_1',
	'campos' => array(
		array("nome" => "nm_email", "tipo" => "texto", "tamanho" => "255", "unico" => "1"),
		array("nome" => "nome", "tipo" => "texto", "tamanho" => "100"),
		array("nome" => "sobrenome", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "ramo_atividade", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "status_allin", "tipo" => "numero")
	));

try
{

	$helper = Mage::helper('fvets_allin');
	$accounts = Mage::getModel('fvets_allin/account')
		->getCollection()
		->addFieldToFilter('website_id', $websiteId);

	foreach ($accounts as $account)
	{
		$customerControll = Mage::getModel('fvets_allin/customers');
		$customerControll->createListIfNotExists($account->getId(), $lista);
	}
} catch (Exception $ex)
{
	Mage::throwException($ex->__toString());
}