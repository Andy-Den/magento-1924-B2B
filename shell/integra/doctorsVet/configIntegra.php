<?php
require_once '../../../app/Mage.php';
umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);
umask(0);

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$codeStore = 'doctorsvet'; // codigo do SoreView
$storeId = 8; // define em qual Distribuidora a integração vai rodar
$websiteId = 5; // Define o Website ID da distribuidora
$storeviewId = 8; // define em qual view da Distribuidora a integração vai rodar
$stockId = 4; // define o stock da distribuidora

$storeView = '8,9,10'; // Definido para essa ocasião
$storeViewAll = '8,9,10';

$arrayStoreView = explode(',', $storeView);
$iStoreView = count($arrayStoreView);

$storeViewReps = '8,9,10';

$idCategoryCorrigir = 412; // define o ID correspondente `a categoria corrigir da Store

$currentDate = date('Y-m-d'); // Pega o data atual da execução do script
$currentDateFormated = date("'Y-m-d H:i:s'"); // Pega o data atual da execução do script


/**
 * Habilita atualização dos emails pelo ERP
 */
$atualizaEmail = false;

/**
 * Permite cadastro de novos produtos
 */
$addNewProducts = false;

/**
 * Habilita o debug para exibir as consultas e detalhes da integraçao
 */
$debug = false;

/**
 * Habilitado quando a integraçao ainda esta em faze de teste
 */
$local = false;

/**
 * Array com ID_ERPs de produtos que devem ser ignorados durante a execução da integração
 */
$ignoreIdErp = array();

/**
 * IDs das tabelas de preços que deverão alimentar os grupos de clientes do site
 */
$idTablePriceValid = array(10002,); //id ERP dos grupos que são enviados pelos ERP e o IntegraVet irá trabalhar

/**
 * Diretório de importação
 */
$directoryImport = "/wwwroot/integracao/$codeStore/importer";
$testDirectoryImport = './exemploCsv/importer';

if ($local == true ): $directoryImp = $testDirectoryImport; else: $directoryImp = $directoryImport; endif;

/**
 * Diretório de exportação
 */
$directoryExport = "/wwwroot/integracao/$codeStore/exporter";
$testDirectoryExport = "./exemploCsv/exporter";

if ($local == true ): $directoryExp = $testDirectoryExport; else: $directoryExp = $directoryExport; endif;

/**
 * Diretório de logs da integração
 */
$directoryReport = "/wwwroot/integracao/$codeStore/report";
$testDirectoryReport = './exemploCsv/report';

if ($local == true ): $directoryRep = $testDirectoryReport; else: $directoryRep = $directoryReport; endif;

// Função para atualizar data de update do registro afetado na integração.
function updateDateCustomer($resource, $writeConnection, $currentDateFormated, $entityId){
    $updateDate = "UPDATE ";
    $updateDate .= $resource->getTableName(customer_entity);
    $updateDate .= " SET updated_at = STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s')";
    $updateDate .= " WHERE entity_id = $entityId";

    $writeConnection->query($updateDate);
}