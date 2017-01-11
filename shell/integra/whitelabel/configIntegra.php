<?php
require_once __DIR__ . '/../../../app/Mage.php';

umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);
umask(0);

array_shift($argv);
if (count($argv) < 5) {
  die('You must provide website_id storeview_id code_store root_category_name id_category_fix');
}

list(
  $websiteId,
  $storeviewId,
  $codeStore,
  $rootCategoryName,
  $idCategoryCorrigir
  ) = $argv;

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$debug = false;
$local = false;

$storeId = 0; // não alterar
$magmiCategoriesStructure = true; //define se a coluna de categorias vai ser na estrutura do magmi

$defaultStore = array(0,1); // nao alterar
$storeView = array($storeviewId); // Definido para essa ocasião --> Omega Purina

$arrayStoreView = explode(',', $storeView);
$iStoreView = count($arrayStoreView);

$arrayWebSites = explode(',', $websiteId);
$iWebsiteId = count($arrayWebSites);

$currentDate = date('Ymd' . '000000'); // Pega o data atual da execução do script
$currentDateFormated = date("Y-m-d H:i:s"); // Pega o data atual da execução do script

/**
 * Diretório de importação
 */
$directoryImport = '/tmp/firstload/';
$testDirectoryImport = './exemploCsv/importer';

if ($local == true ): $directoryImp = $testDirectoryImport; else: $directoryImp = $directoryImport; endif;

