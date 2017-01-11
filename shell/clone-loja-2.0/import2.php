<?php
require_once 'abstract.php';

//ID do Website e Storeview da loja cujos dados serão copiados
$store = getStoreByCode($_fromStoreviewCode);
$_fromWebsiteId = $store->getWebsiteId();
$_fromStoreViewId = $store->getStoreId();
//fim

//1. ===== Cópia dos dados de configuração =====
$store = Mage::getModel('core/store');
$store = getStoreByCode($_toNewCode);
$storeId = $store->getStoreId();
$websiteId = $store->getWebsiteId();

$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

if ($_createNewWebsite) {
	$_sql = "
SET @to_website := $websiteId;
SET @to_store := $storeId;
SET @from_website := $_fromWebsiteId;
SET @from_store := $_fromStoreViewId;

DELETE FROM core_config_data
WHERE scope = 'websites'
AND scope_id = @to_website;
DELETE FROM core_config_data
WHERE scope = 'stores'
AND scope_id = @to_store;

INSERT INTO core_config_data (
scope,
scope_id,
path,
value
) SELECT
scope,
@to_website,
path,
value
FROM core_config_data
WHERE scope = 'websites'
AND scope_id = @from_website;

INSERT INTO core_config_data (
scope,
scope_id,
path,
value
) SELECT
scope,
@to_store,
path,
value
FROM core_config_data
WHERE scope = 'stores'
AND scope_id = @from_store;
";
} else {
	$_sql = "
SET @to_website := $websiteId;
SET @to_store := $storeId;
SET @from_website := $_fromWebsiteId;
SET @from_store := $_fromStoreViewId;

DELETE FROM core_config_data
WHERE scope = 'stores'
AND scope_id = @to_store;

INSERT INTO core_config_data (
scope,
scope_id,
path,
value
) SELECT
scope,
@to_store,
path,
value
FROM core_config_data
WHERE scope = 'stores'
AND scope_id = @from_store;
";
}
//2. ===== Setando parâmetros de configuração =====
$connection->query($_sql);
$store = Mage::getModel('core/store');
$store = getStoreByCode($_toNewCode);
$storeId = $store->getStoreId();
$websiteId = $store->getWebsiteId();

$_config = new Mage_Core_Model_Config();
$_options = array(
	'web/url/use_store' => '0',
	'design/theme/locale' => $_toNewCode,
	'design/theme/template' => $_toNewCode,
	'design/theme/skin' => $_toNewCode,
	'design/theme/layout' => $_toNewCode,
	'web/secure/base_url' => 'http://' . $_toNewCode . '.local/',
	'web/unsecure/base_url' => 'http://' . $_toNewCode . '.local/',
	'general/store_information/name' => $_toNewCode,
	'general/country/default' => 'BR',
	'general/country/allow' => 'BR',
);

if ($_createNewWebsite) {
	foreach ($_options as $_path => $_value) {
		$_config->saveConfig($_path, $_value, 'websites', $websiteId);
	}
}
foreach ($_options as $_path => $_value) {
	$_config->saveConfig($_path, $_value, 'stores', $storeId);
}

//3. ===== Copiar dados dos blocos e páginas estáticas (CMS) =====
if ($_copyCmsAndStaticBlocks) {
$store = Mage::getModel('core/store');
$store = getStoreByCode($_toNewCode);
$storeId = $store->getStoreId();
$websiteId = $store->getWebsiteId();

	$_sql = "
# DEFINE
SET @to_website := $websiteId;
SET @to_store := $storeId;
SET @from_website := $_fromWebsiteId;
SET @from_store := $_fromStoreViewId;

/** cms_block_store */
DELETE FROM cms_block_store
WHERE store_id = @to_store;

INSERT INTO cms_block_store (
    block_id,
    store_id
) SELECT
    block_id,
    @to_store
FROM cms_block_store
WHERE store_id = @from_store;

/** cms_page_store */
DELETE FROM cms_page_store
WHERE store_id = @to_store;

INSERT INTO cms_page_store (
    page_id,
    store_id
) SELECT
    page_id,
    @to_store
FROM cms_page_store
WHERE store_id = @from_store;
";
	$connection->query($_sql);
}

function getStoreByCode($storeCode)
{
	$stores = array_keys(Mage::app()->getStores());
	foreach ($stores as $id) {
		$store = Mage::app()->getStore($id);
		if ($store->getCode() == $storeCode) {
			return $store;
		}
	}
	return null; // if not found
}