<?php
$pathToImport = __DIR__ . '/extras/';
require_once __DIR__.'/../../app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 11/6/15
 * Time: 4:11 PM
 */

$storeCodeToImport = "disprol";

$store = Mage::getModel('core/store')->getCollection()
	->addFieldToFilter('code', $storeCodeToImport)
	->getFirstItem();

if (!$store)
{
	die("Código da store é inválido");
}

$website = Mage::getModel('core/website')->load($store->getWebsiteId());

//=== core_config_data ===

$lines = file($pathToImport . $storeCodeToImport . "_config_content.json", FILE_IGNORE_NEW_LINES);

foreach ($lines as $line)
{
	$content = Mage::helper('core')->jsonDecode($line);

	$websiteConfigs = Mage::getModel('core/config_data')->getCollection()
		->addFieldToFilter('scope', $content['scope'])
		->addFieldToFilter('scope_id', ($content['scope'] == 'websites' || $content['scope'] == 'default') ? $website->getId() : $store->getId())
		->addFieldToFilter('path', $content['path'])
		->getFirstItem();

	if (!$websiteConfigs->getConfigId())
	{
		$websiteConfigs = Mage::getModel('core/config_data');
		unset($content['config_id']);
		$websiteConfigs->setData($content);

		$websiteConfigs->setScopeId(($content['scope'] == 'websites' || $content['scope'] == 'default') ? $website->getId() : $store->getId());
		$websiteConfigs->save();
	} else
	{
		$websiteConfigs->setValue($content['value']);
		$websiteConfigs->save();
	}
}

//=== widget_instance ===

$lines = file($pathToImport . $storeCodeToImport . "_widget_content.json", FILE_IGNORE_NEW_LINES);

foreach ($lines as $line)
{
	$content = Mage::helper('core')->jsonDecode($line);

	$widget = Mage::getModel('widget/widget_instance')->getCollection()
		->addFieldToFilter('instance_type', $content['instance_type'])
		->addFieldToFilter('package_theme', $content['package_theme'])
		->addFieldToFilter('title', $content['title'])
		->addFieldToFilter('widget_parameters', $content['widget_parameters'])
		->getFirstItem();

	if (!$widget->getInstanceId())
	{
		$widget = Mage::getModel('widget/widget_instance');
		unset($data['instance_id']);
		$widget->setData($data);

		$widget->setStoreIds($store->getId());
		$widget->save();
	} else
	{
		$newStoreIds = $widget->getStoreIds();
		if (!in_array($store->getId(), $newStoreIds))
		{
			$newStoreIds[] = $store->getId();
			$widget->setStoreIds(implode(',', $newStoreIds));
			$widget->save();
		}
	}
}

//=== cms_block ===

$lines = file($pathToImport . $storeCodeToImport . "_cms_block_content.json", FILE_IGNORE_NEW_LINES);

foreach ($lines as $line)
{
	$data = Mage::helper('core')->jsonDecode($line);

	$block = Mage::getModel('cms/block')->getCollection()
		->addFieldToFilter('title', $data['title'])
		->getFirstItem();

	if (!$block->getBlockId())
	{
		$block = Mage::getModel('cms/block');
		unset($data['block_id']);
		$block->setData($data);
		$block->setStores(array($store->getId()));
		$block->save();
	} else
	{
		$block->load();
		$newStoreIds = $block->getStores();
		if (!$newStoreIds || (in_array(0, $newStoreIds)))
		{
			continue;
		}
		if (!in_array($store->getId(), $newStoreIds))
		{
			$newStoreIds[] = $store->getId();
			$block->setStores($newStoreIds);
			$block->save();
		}
	}
}

//=== cms_page ===

$lines = file($pathToImport . $storeCodeToImport . "_cms_page_content.json", FILE_IGNORE_NEW_LINES);

foreach ($lines as $line)
{
	$data = Mage::helper('core')->jsonDecode($line);

	$block = Mage::getModel('cms/page')->getCollection()
		->addFieldToFilter('title', $data['title'])
		->getFirstItem();

	if (!$block->getPageId())
	{
		$block = Mage::getModel('cms/page');
		unset($data['page_id']);
		$block->setData($data);
		$block->setStoreId(array($store->getId()));
		$block->save();
	} else
	{
		$block->load();
		$newStoreIds = $block->getStoreId();
		if (!$newStoreIds || (in_array(0, $newStoreIds)))
		{
			continue;
		}
		if (!in_array($store->getId(), $newStoreIds))
		{
			$newStoreIds[] = $store->getId();
			$block->setStoreId($newStoreIds);
			$block->save();
		}
	}
}

//=== warehouse

$stock = Mage::getModel('cataloginventory/stock')->getCollection()
	->addFieldToFilter('lower(stock_name)', $store->getCode())
	->getFirstItem();

if (!$stock->getStockId())
{
	$stock = Mage::getModel('cataloginventory/stock');
	$stock->setData('stock_name', ucfirst($store->getCode()));
	$stock->save();
}

$lines = file($pathToImport . $storeCodeToImport . "_warehouse_content.json", FILE_IGNORE_NEW_LINES);

$line = current($lines);
$data = Mage::helper('core')->jsonDecode($line);

if ($data)
{
	$warehouse = Mage::getModel('warehouse/warehouse')->getCollection()
		->addFieldToFilter('code', $store->getCode())
		->getFirstItem();

	if (!$warehouse->getWarehouseId())
	{
		unset($data['warehouse_id']);
		$data['stock_id'] = $stock->getStockId();
		$warehouse->setData($data);
		$warehouse->setStoreIds(array($store->getId()));
		$warehouse->save();
	}
}

//=== transactionals

$lines = file($pathToImport . $storeCodeToImport . "_transactional_content.json", FILE_IGNORE_NEW_LINES);

foreach ($lines as $line)
{
	$data = Mage::helper('core')->jsonDecode($line);

	$template = Mage::getModel('core/email_template')->getCollection()
		->addFieldToFilter('template_code', $data['template_code'])
		->getFirstItem();

	if (!$template->getTemplateId())
	{
		unset($data['template_id']);
		$template->setData($data);
		$template->save();
	}
}
