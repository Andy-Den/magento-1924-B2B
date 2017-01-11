<?php
$pathBase = '/wwwroot/whitelabel/current/';
#$pathBase = '/wwwroot/current/';
require_once "{$pathBase}/app/Mage.php";
umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);
umask(0);

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$debug = false;
$local = false;

$mngrStock = false;

$codeStore = 'peclam'; // codigo do
$storeId = 2; // define em qual Distribuidora a integração vai rodar
$websiteId = 2; // Define o Website ID da distribuidora
$storeviewId = 2; // define em qual view da Distribuidora a integração vai rodar
$stockId = 2; // define o stock da distribuidora

$storeView = array(2); // Definido para essa ocasião

$storeViewAll = '2';
$storeViewReps = '2'; // Define os grupos de acessos dos representantes;

$idCategoryCorrigir = 229; // define o ID correspondente `a categoria corrigir da Store

$attributeProductPrice = 75; // attribute_id referente ao atributo preço do produto
$attributeProductSpecialPrice = 76; // attribute_id referente ao atributo special price do produto
$attributeProductPriceFromDate = 77; // attribute_id referente a data de início da promoção
$attributeProductPriceEndDate = 78; // attribute_id referente a data final da promoção

$currentDate = date('Ymd' . '000000'); // Pega o data atual da execução do script
$currentDateFormated = date("'Y-m-d H:i:s'"); // Pega o data atual da execução do script

/**
 * Diretório de importação
 */
$directoryImport = "/wwwroot/whitelabel/integracao/$codeStore/importer";
$testDirectoryImport = './exemploCsv/importer';

if ($local == true ): $directoryImp = $testDirectoryImport; else: $directoryImp = $directoryImport; endif;

/**
 * Diretório de exportação
 */
$directoryExport = "/wwwroot/whitelabel/integracao/$codeStore/exporter";
$testDirectoryExport = "./exemploCsv/exporter";

if ($local == true ): $directoryExp = $testDirectoryExport; else: $directoryExp = $directoryExport; endif;

/**
 * Diretório de logs da integração
 */
$directoryReport = "/wwwroot/whitelabel/integracao/$codeStore/report";
$testDirectoryReport = './exemploCsv/report';

// Função para atualizar data de update do registro afetado na integração.
function updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId){
    $updateDate = "UPDATE ";
    $updateDate .= $resource->getTableName(customer_entity);
    $updateDate .= " SET updated_at = STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s')";
    $updateDate .= " WHERE entity_id = $entityId";

    $writeConnection->query($updateDate);
}
