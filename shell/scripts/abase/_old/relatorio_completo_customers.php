<?php
require_once './configScript.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/17/16
 * Time: 2:51 PM
 */

$totalClientesMagento = 0;
$clientesAtivos = 0;
$clientesRestritosZoetis = 0;
$clientesRestritosVacinas = 0;
$naoCadastradosMagentoPoremArquivoIntegracao = array();
$naoCadastradosMagentoNemNoArquivoIntegracao = array();
$clientesDuplicadosArquivoAndre = array();
$clientesDuplicadosArquivoIntegracao = array();

$lines = file("extras/20160517_customers.csv", FILE_IGNORE_NEW_LINES);
$customersIntegracao = array();
$firstLine = true;
if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	foreach ($lines as $line) {
		if ($firstLine) {
			$firstLine = false;
			continue;
		}
		$customersIntegracao[] = explode('|', $line);
	}
}

$lines = file("extras/20160511_carteiras ativas abaseonline.csv", FILE_IGNORE_NEW_LINES);
$customersArqAndre = array();
$firstLine = true;
if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	foreach ($lines as $line) {
		if ($firstLine) {
			$firstLine = false;
			continue;
		}
		$customersArqAndre[] = explode('|', $line);
	}
}

//=== total de customers no site ===
$customersMagento = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToSelect('id_erp')
	->addAttributeToFilter('website_id', $websiteId);
$totalClientesMagento = count($customersMagento);
echo 'Total de clientes registrados no Magento: ' . $totalClientesMagento . "\n";

$customersMagentoArray = [];
foreach ($customersMagento as $customer) {
	$customersMagentoArray[$customer->getIdErp()] = $customer;
}
//=== fim ===

//=== total de customers no arquivo do André ===
echo 'Total de clientes registrados no Arquivo do André: ' . count($customersArqAndre) . "\n";
//=== fim ===

//=== total de customers no arquivo de integração ===
echo 'Total de clientes registrados no Arquivo de integração: ' . count($customersIntegracao) . "\n";
//=== fim ===

//=== total de customers ativos no site ===
$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToFilter('website_id', $websiteId)
	->addFieldToFilter('is_active', 1);
$clientesAtivos = count($customers);
echo 'Total de clientes ativos registrados no Magento: ' . $clientesAtivos . "\n";
//=== fim ===

//=== total de customers ativos / inativos no magento
$count = 0;
foreach($customersArqAndre as $customer) {
	if ($customer[1] == 'SIM') {
		$count++;
	}
}
echo 'Total de clientes ativos no Arquivo do André: ' . $count . "\n";
//=== fim ===

//=== total de customers com restriçao ZOETIS no site ===
$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToFilter('website_id', $websiteId)
	->addFieldToFilter('restricted_brands', array('neq' => 'NULL'));
$clientesRestritosZoetis = count($customers);
echo 'Total de clientes ativos com restrição ZOETIS registrados no Magento: ' . $clientesRestritosZoetis . "\n";
//=== fim ===

//=== total de customers com restrição ZOETIS no arquivo do Andrés
$count = 0;
foreach($customersArqAndre as $customer) {
	if ($customer[2] != 'SIM') {
		$count++;
	}
}
echo 'Total de clientes com restrição ZOETIS no Arquivo do André: ' . $count . "\n";
//=== fim ===

//=== total de customers com restriçao de Vacinas no site ===
$restrictionGroupId = 3;
$customers = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup')
	->load($restrictionGroupId)
	->getSelectedCustomers();
$clientesRestritosVacinas = count($customers);
echo 'Total de clientes ativos com restrição VACINAS registrados no Magento: ' . $clientesRestritosVacinas . "\n";
//=== fim ===

//=== total de customers com restrição VACINAS no arquivo do Andrés
$count = 0;
foreach($customersArqAndre as $customer) {
	if ($customer[3] != 'SIM') {
		$count++;
	}
}
echo 'Total de clientes com restrição VACINAS no Arquivo do André: ' . $count . "\n";
//=== fim ===

//=== total de customers não cadastrados no Magento porém sim / não no arquivo de integração;
foreach ($customersArqAndre as $customer) {
	$customerIdErp = str_pad($customer[4], 6, "0", STR_PAD_LEFT);
	if (!isset($customersMagentoArray[$customerIdErp])) {
		$customers = getCustomerFromIntegrationFile($customerIdErp);
		if (!empty($customers)) {
			$naoCadastradosMagentoPoremArquivoIntegracao[$customer[4]] = $customer;
		} else {
			$naoCadastradosMagentoNemNoArquivoIntegracao[$customer[4]] = $customer;
		}
	}
}
echo 'Total de clientes não cadastrados no magento porém CONSTAM no arquivo de integração (planilha em anexo): ' . count($naoCadastradosMagentoPoremArquivoIntegracao) . "\n";
$print = '';
foreach ($naoCadastradosMagentoPoremArquivoIntegracao as $item) {
	$print = $print . (implode("|", $item) . "\n");
}
printFile('naoCadastradosMagentoPoremArquivoIntegracao', $print);
echo 'Total de clientes não cadastrados no magento e que NÃO CONSTAM no arquivo de integração (planilha em anexo): ' . count($naoCadastradosMagentoNemNoArquivoIntegracao) . "\n";
$print = '';
foreach ($naoCadastradosMagentoNemNoArquivoIntegracao as $item) {
	$print = $print . (implode("|", $item) . "\n");
}
printFile('naoCadastradosMagentoNemNoArquivoIntegracao', $print);
//=== fim ===

//=== Cadastros duplicados no arquivo do André ===
$tmp = array();
foreach ($customersArqAndre as $customer) {
	if (!isset($tmp[$customer[4]])) {
		$tmp[$customer[4]] = $customer;
	} else {
		$clientesDuplicadosArquivoAndre[] = $customer;
	}
}
echo 'Total de clientes duplicados (pelo cód. do cliente) no arquivo do André (planilha em anexo): ' . count($clientesDuplicadosArquivoAndre) . "\n";
$print = '';
foreach ($clientesDuplicadosArquivoAndre as $item) {
	$print = $print . (implode("|", $item) . "\n");
}
printFile('clientesDuplicadosArquivoAndre', $print);
// === fim ===
//=== Cadastros duplicados no arquivo de integração ===
$tmp = array();
foreach ($customersIntegracao as $customer) {
	if (!isset($tmp[$customer[20]])) {
		$tmp[$customer[20]] = $customer;
	} else {
		$clientesDuplicadosArquivoIntegracao[] = $customer;
	}
}
echo 'Total de clientes duplicados (pelo email) no arquivo de integração (planilha em anexo) : ' . count($clientesDuplicadosArquivoIntegracao) . "\n";
$print = '';
foreach ($customersIntegracao as $item) {
	$print = $print . (implode("|", $item) . "\n");
}
printFile('clientesDuplicadosArquivoIntegracao', $print);
// === fim ===

function getCustomer($idErp)
{
	global $websiteId;
	$customer = Mage::getModel('customer/customer')
		->getCollection()
		->addAttributeToFilter('website_id', $websiteId)
		->addAttributeToFilter(
			array(
				array('attribute' => 'id_erp', 'eq' => str_pad($idErp, 6, "0", STR_PAD_LEFT)),
				array('attribute' => 'id_erp', 'eq' => $idErp)
			)
		)
		->getFirstItem();

	return $customer;
}

function getCustomerFromIntegrationFile($idErp)
{
	global $customersIntegracao;
	$line = array();
	foreach ($customersIntegracao as $customer) {
		if ($customer[1] == $idErp) {
			$line[] = $customer;
		}
	}
	return $line;
}

function printFile($fileName, $data)
{
	global $websiteId;
	$myfile = fopen('extras/' . date('Ymd') . "_" . $fileName . "_" . $websiteId . ".csv", "w") or die("Unable to open file!");
	fwrite($myfile, $data);
	fclose($myfile);
}