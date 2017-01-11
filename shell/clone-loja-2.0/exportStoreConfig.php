<?php
require_once __DIR__ . '/abstract.php';
$pathToExport = __DIR__ . '/extras/';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 11/6/15
 * Time: 3:04 PM
 */

$storeCodeToExport = "disprol";

$store = Mage::getModel('core/store')->getCollection()
	->addFieldToFilter('code', $storeCodeToExport)
	->getFirstItem();

if (!$store)
{
	die("Código da store é inválido");
}

$website = Mage::getModel('core/website')->load($store->getWebsiteId());

//=== core_config_data ===

$websiteConfigs = Mage::getModel('core/config_data')->getCollection()
	->addFieldToFilter('scope', 'websites')
	->addFieldToFilter('scope_id', $website->getId());

$configContent = '';

foreach ($websiteConfigs as $config)
{
	$configContent .= Mage::helper('core')->jsonEncode($config) . "\n";
}

$storeConfigs = Mage::getModel('core/config_data')->getCollection()
	->addFieldToFilter('scope', 'stores')
	->addFieldToFilter('scope_id', $store->getId());

foreach ($storeConfigs as $config)
{
	$configContent .= Mage::helper('core')->jsonEncode($config) . "\n";
}

file_put_contents($pathToExport . $storeCodeToExport . '_config_content.json', $configContent);

//=== widget_instance ===

$widgets = Mage::getModel('widget/widget_instance')->getCollection()
	->addFieldToFilter('store_ids', array('finset' => $store->getId()));

$widgetContent = '';

foreach ($widgets as $widget)
{
	$widgetContent .= Mage::helper('core')->jsonEncode($widget) . "\n";
}

file_put_contents($pathToExport . $storeCodeToExport . '_widget_content.json', $widgetContent);

//=== cms_block ===

$blocks = Mage::getModel('cms/block')->getCollection()
	->addStoreFilter($store->getId());

$cmsBlockContent = '';

foreach ($blocks as $block)
{
	$cmsBlockContent .= Mage::helper('core')->jsonEncode($block) . "\n";
}

file_put_contents($pathToExport . $storeCodeToExport . '_cms_block_content.json', $cmsBlockContent);

//=== cms_page ===

$blocks = Mage::getModel('cms/page')->getCollection()
	->addStoreFilter($store->getId());

$cmsPageContent = '';

foreach ($blocks as $block)
{
	$cmsPageContent .= Mage::helper('core')->jsonEncode($block) . "\n";
}

file_put_contents($pathToExport . $storeCodeToExport . '_cms_page_content.json', $cmsPageContent);

//=== warehouse

$warehouse = Mage::getModel('warehouse/warehouse')->getCollection();
$warehouse->getSelect()->joinInner(array('ws' => 'warehouse_store'), 'ws.warehouse_id = main_table.warehouse_id and ws.store_id = ' . $store->getId(), array());
$warehouse = $warehouse->getFirstItem();

if ($warehouse->getWarehouseId())
{
	$warehouseContent = Mage::helper('core')->jsonEncode($warehouse);
	file_put_contents($pathToExport . $storeCodeToExport . '_warehouse_content.json', $warehouseContent);
}

//=== transactionals

$transactionals = Mage::getModel('core/email_template')->getCollection()
	->addFieldToFilter('lower(template_code)', array('like' => ($store->getCode() . "%")));

$transactionalsContent = '';
foreach ($transactionals as $transactional)
{
	$transactionalsContent .= Mage::helper('core')->jsonEncode($transactional) . "\n";
}

if ($transactionalsContent)
{
	file_put_contents($pathToExport . $storeCodeToExport . '_transactional_content.json', $transactionalsContent);
}