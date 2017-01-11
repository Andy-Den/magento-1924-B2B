<?php
$pathBase = '/wwwroot/current/';
require_once $pathBase . 'app/Mage.php';
umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);
umask(0);

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

$codeStore = 'petvale'; // codigo do SoreView
$storeId = 11; // define em qual Distribuidora a integração vai rodar
$websiteId = 6; // Define o Website ID da distribuidora
$storeviewId = 11; // define em qual view da Distribuidora a integração vai rodar
$stockId = '6'; // define o stock que devereão ser alimentados

$arrayStockId = explode(',', $stockId);
$iStockId = count($arrayStockId);

$storeView = '11,12,13,14,21'; // Definido para trabalhar em todas views da Omega
$storeViewAll = '11,12,13,14,21'; // Definido para trabalhar em todas views da Omega
$storeViewMsd = '12,21'; // Definido para trabalhar nas views da MSD

$arrayStoreView = explode(',', $storeView);
$iStoreView = count($arrayStoreView);

$storeViewReps = '11,12,13,14,21'; // Define os grupos de acessos dos representantes;

$idCategoryCorrigir = 546; // define o ID correspondente `a categoria corrigir da Store

$currentDate = date('Y-m-d'); // Pega o data atual da execução do script
$currentDateFormated = date("'Y-m-d H:i:s'"); // Pega o data atual da execução do script

$channel = 'integration'; // Canal onde será enviado os reports da integração 
$errorChannel = 'errors';

/**
 * Habilita atualização dos emails pelo ERP
 */
$atualizaEmail = true;

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
 * Array com ID_ERPs que devem ser ignorados durante a execução da integração
 */
$ignoreIdErp = array();

/**
 * define os produtos da visão criador que deverão aparecer apenas na visão: 14
 * Esses SKUs não deverão aparecer nas demais visões.
 */
$idErpCriador = array(4026, 4027, 4051, 4052, 4065, 4066, 4067, 4068, 4069, 4070, 4071, 4074, 4085, 4087, 4088, 4126, 4127, 4142, 4176, 4177, 4233, 151, 4025, 4141, 219, 221, 222, 3344, 3345, 3346, 3755, 3756, 4172, 4178);
$idErpVets = array(3424, 4231, 3422, 3425, 3426, 3735, 3454, 3423, 3452);


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
function updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId){
	$updateDate = "UPDATE ";
	$updateDate .= $resource->getTableName(customer_entity);
	$updateDate .= " SET updated_at = STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s')";
	$updateDate .= " WHERE entity_id = $entityId";

	$writeConnection->query($updateDate);
}