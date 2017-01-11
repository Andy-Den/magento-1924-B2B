<?php
require_once '../configIntegra.php';

require_once $pathBase . 'app/code/local/TM/ProLabels/Model/Mysql4/Label.php';

$brandsToIgnore = array(241);

function getProductByIdErp($idErp, $storeviewId)
{
	global $readConnection;

	$query = "SELECT entity_id FROM catalog_product_entity_varchar WHERE attribute_id = 185 AND store_id IN (" . $storeviewId . ") AND `value` = " . $idErp;
	$productId = $readConnection->fetchOne($query);
	if ($productId)
	{
		return Mage::getModel('catalog/product')->load($productId);
	}
	return null;
}

function getProductByIdErpWebsite($idErp) {
	global $websiteId;

	$product = Mage::getModel('catalog/product')->getCollection()
		->addWebsiteFilter($websiteId)
		->addAttributeToFilter('id_erp', $idErp)
		->getFirstItem();

	return $product;
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

		if (!$product) {
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