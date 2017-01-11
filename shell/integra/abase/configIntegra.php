<?php
$pathBase = '/wwwroot/whitelabel/current/';
require_once $pathBase . 'app/Mage.php';
umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);
umask(0);

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

$codeStore = 'abase'; // codigo do SoreView
$storeId = 16; // define em qual Distribuidora a integração vai rodar
$websiteId = 8; // Define o Website ID da distribuidora
$storeviewId = 16; // define em qual view da Distribuidora a integração vai rodar
$stockId = '9'; // define o stock que devereão ser alimentados

$arrayStockId = explode(',', $stockId);
$iStockId = count($arrayStockId);

$storeView = '16'; // Definido para trabalhar em todas views da Abase
$storeViewAll = '16'; // Definido para trabalhar em todas views da Abase

$arrayStoreView = explode(',', $storeView);
$iStoreView = count($arrayStoreView);

$storeViewReps = '16'; // Define os grupos de acessos dos representantes;

$idCategoryCorrigir = 696; // define o ID correspondente `a categoria corrigir da Store

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
$addNewProducts = true;

/**
 * Habilita o debug para exibir as consultas e detalhes da integraçao
 */
$debug = true;

/**
 * Habilitado quando a integraçao ainda esta em faze de teste
 */
$local = false;

/**
 * Array com ID_ERPs que devem ser ignorados durante a execução da integração
 */
$ignoreIdErp = array();

/**
 * ID ERPs que podem ser vendidos apenas para veterinários
 */

$idErpVets = array('ABI.00274','ABI.00273','ABI.00084','ABI.00275','ABI.00276','ABI.00035','ABI.00107','ABI.00277','ABI.00278','ABI.00271','AAN.00309','ASP.00294','AAM.00292','AAN.00271','AAN.00081');

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

if ($local == true ): $directoryRep = $testDirectoryReport; else: $directoryRep = $directoryReport; endif;

// Função para atualizar data de update do registro afetado na integração.
function updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId){
	$updateDate = "UPDATE ";
	$updateDate .= $resource->getTableName(customer_entity);
	$updateDate .= " SET updated_at = STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s')";
	$updateDate .= " WHERE entity_id = $entityId";

	$writeConnection->query($updateDate);
}