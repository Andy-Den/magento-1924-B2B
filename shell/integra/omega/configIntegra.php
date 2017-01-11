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

$codeStore = 'omega'; // codigo do SoreView
$storeId = 4; // define em qual Distribuidora a integração vai rodar
$websiteId = 4; // Define o Website ID da distribuidora
$storeviewId = 4; // define em qual view da Distribuidora a integração vai rodar
$stockId = '1,3,5'; // define o stock que devereão ser alimentados

$arrayStockId = explode(',', $stockId);
$iStockId = count($arrayStockId);

$storeView = '4,5,6,7'; // Definido para trabalhar em todas views da Omega
$storeViewAll = '4,5,6,7'; // Definido para trabalhar em todas views da Omega
$storeViewMsd = '5,7'; // Definido para trabalhar nas views da MSD
$storeViewPurina = '4,6'; // Definido para trabalhar nas views da Purina

$arrayStoreView = explode(',', $storeView);
$iStoreView = count($arrayStoreView);

$storeViewReps = '4,5,6,7'; // Define os grupos de acessos dos representantes;

$idCategoryCorrigir = 518; // define o ID correspondente `a categoria corrigir da Store

$currentDate = date('Y-m-d'); // Pega o data atual da execução do script
$currentDateFormated = date("'Y-m-d H:i:s'"); // Pega o data atual da execução do script

$channel = 'integration';
$errorChannel = 'errors';

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
 * Array com ID_ERPs que devem ser ignorados durante a execução da integração
 */
$ignoreIdErp = array(1,);

/**
 * Monta a união dos grupos de tabelas deve-se definir os ids do IntegraVets
 * exemplo da aplicação:
 * 'nome_da_união' =>  [id_grupo1, id_grupo2, id_grupo1+2]
 * o último ID é sempre da tabela virtual que uni os grupos
 */
$unions = [
	'A_MSD'     => [14,12,18],
	'A_KA_MSD'  => [14,13,19],
	'B_MSD'     => [15,12,20],
	'B_KA_MSD'  => [15,13,21],
	'C_MSD'     => [16,12,22],
	'C_KA_MSD'  => [16,13,23],
	'D_MSD'     => [17,12,24],
	'D_KA_MSD'  => [17,13,25],
];

/**
 * Diretório de importação
 */
$directoryImport = '/wwwroot/integracao/omega/importer';
$testDirectoryImport = './exemploCsv/importer';

if ($local == true ): $directoryImp = $testDirectoryImport; else: $directoryImp = $directoryImport; endif;

/**
 * Diretório de exportação
 */
$directoryExport = "/wwwroot/integracao/omega/exporter";
$testDirectoryExport = "./exemploCsv/exporter";

if ($local == true ): $directoryExp = $testDirectoryExport; else: $directoryExp = $directoryExport; endif;

/**
 * Diretório de logs da integração
 */
$directoryReport = '/wwwroot/integracao/doctorsvet/report';
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
