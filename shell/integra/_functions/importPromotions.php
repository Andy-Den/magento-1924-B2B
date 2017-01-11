<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 8/26/15
 * Time: 10:56 AM
 */

require_once '../../../app/code/local/TM/ProLabels/Model/Mysql4/Label.php';

$brandsToIgnore = array();

function validateBrands($keyToArray)
{
	global $storeView;
	global $brandsToIgnore;

	if (!is_array($keyToArray))
	{
		$keyToArray = explode(',', $keyToArray);
	}

	foreach ($keyToArray as $key => $idErp)
	{
		$product = getProductByIdErp($idErp, explode(',', $storeView)[0]);

		if (!$product)
		{
			continue;
		}

		$categoryIds = $product->getCategoryIds();

		foreach ($categoryIds as $categoryId)
		{
			if (in_array($categoryId, $brandsToIgnore))
			{
				unset($keyToArray[$key]);
				break;
			}
		}
	}

	return implode(',', $keyToArray);
}

//======================================= labels =========================================
function createLabels($sku, $_labelShortName)
{
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
	if ($product)
	{
		$ruleId = getLastRuleIdAdded();
		saveLabel($ruleId, $product->getId(), $_labelShortName);
	}
}

function saveLabel($ruleId, $productId, $shortName)
{
	global $resource;
	global $writeConnection;
	$salesruleLabel = "INSERT IGNORE INTO ";
	$salesruleLabel .= $resource->getTableName('fvets_salesrule_label');
	$salesruleLabel .= "(salesrule_id, product_id, short_name) ";
	$salesruleLabel .= "VALUES ($ruleId, $productId, '$shortName');";
	$writeConnection->query($salesruleLabel);
}

function getLastRuleIdAdded()
{
	global $resource;
	global $readConnection;
	global $codeStore;
	// Pega o rule_id da ultima regra adicionada
	$getRuleId = "SELECT rule_id FROM ";
	$getRuleId .= $resource->getTableName('salesrule');
	$getRuleId .= " where name like '{$codeStore}%' ORDER BY rule_id DESC LIMIT 1;";
	$ruleId = $readConnection->fetchOne($getRuleId);
	return $ruleId;
}

function cleanCampaigns()
{
	global $resource;
	global $writeConnection;
	global $codeStore;
	global $websiteId;
	/**
	 * Remove todas as regras deste website
	 */
	$removeRegraPromocao = "DELETE from ";
	$removeRegraPromocao .= $resource->getTableName('salesrule');
	$removeRegraPromocao .= " WHERE name LIKE '" . ($codeStore . "_%") . "' or name LIKE '" . ($codeStore . "-%") . "'";

	$writeConnection->query($removeRegraPromocao);

	/**
	 * Remove todos os labels da regra da promocao
	 */
	$removeLabels = "DELETE " . $resource->getTableName('fvets_salesrule_label') . " FROM ";
	$removeLabels .= $resource->getTableName('fvets_salesrule_label');
	$removeLabels .= " join " . $resource->getTableName('salesrule') . " sr on sr.rule_id = salesrule_id";
	$removeLabels .= " join " . $resource->getTableName('salesrule_website') . " sw on sr.rule_id = sw.rule_id";
	$removeLabels .= " where sw.website_id = ({$websiteId}) and (sr.name like '" . ($codeStore . "_%") . "' or name LIKE '" . ($codeStore . "-%") . "')";

	$writeConnection->query($removeLabels);
}

//========================================================================================

function getProductByIdErp($idErp, $storeId = null)
{
	global $websiteId;

	if (!$storeId)
	{
		$stores = Mage::getModel('core/website')->load($websiteId)->getStores();

		foreach ($stores as $store)
		{
			$select = Mage::getModel('catalog/product')->getCollection()
				->addWebsiteFilter($websiteId)
				->setStoreId($store->getId())
				->addAttributeToFilter('id_erp', $idErp);

			$product = $select
				->getFirstItem();
			if ($product->getId())
			{
				return $product->setStoreId($store->getId())->load();
			}
		}
	} else
	{
		$select = Mage::getModel('catalog/product')->getCollection()
			->addWebsiteFilter($websiteId)
			->setStoreId($storeId)
			->addAttributeToFilter('id_erp', $idErp);

		$product = $select
			->getFirstItem();
		if ($product->getId())
		{
			return $product->setStoreId($storeId)->load();
		}
	}

	return null;
}

function getSkuByIdErp($idErp, $storeId)
{
	global $resource;
	global $readConnection;

	$skuErp = ltrim($idErp, '0');

	$getSku = "SELECT cpe.sku FROM ";
	$getSku .= $resource->getTableName(catalog_product_entity_varchar) . " as cpev ";
	$getSku .= "INNER JOIN ";
	$getSku .= $resource->getTableName(catalog_product_entity) . " as cpe ";
	$getSku .= "ON cpev.entity_id = cpe.entity_id ";
	$getSku .= " WHERE cpev.`value` = '$skuErp' AND cpev.attribute_id = 185 AND cpev.store_id = $storeId;";

	$sku = $readConnection->fetchOne($getSku);

	if (!$sku)
	{
		return null;
	}

	return $sku;
}