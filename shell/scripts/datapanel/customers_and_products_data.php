<?php
require_once 'config.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/1/15
 * Time: 4:40 PM
 */

foreach ($websitesId as $websiteId) {

	$websiteCode = getWebsiteCode($websiteId);

	foreach ($collectionType as $keyType => $type) {

		$entityDataString = '';

		$attributesCollection = null;

		if ($keyType == 'customer') {
			$collection = Mage::getModel($type)->getCollection();
			$collection->addAttributeToFilter('website_id', $websiteId);
			$collection->addAttributeToSelect('*');
		} elseif ($keyType == 'product') {
			$collection = Mage::getResourceModel('catalog/product_collection');
			$collection->addWebsiteFilter($websiteId);
			$collection->addAttributeToSelect('*');
			$collection->load();
		}

		$header = '';
		$headerDone = false;

		foreach ($collection as $item) {

			if (!$attributesCollection) {
				$attributesCollection = $item->getAttributes();
			}
			foreach ($attributesCollection as $attributeLabel => $attributeValue) {
				if ($attributeValue->getAttributeCode() == 'description' || $attributeValue->getAttributeCode() == 'short_description' || $attributeValue->getAttributeCode() == 'epi' || $attributeValue->getAttributeCode() == 'email' || $attributeValue->getAttributeCode() == 'secondary_email') {
					continue;
				}
				if (!$headerDone) {
					$header .= $attributeValue->getAttributeCode() . '|';
				}
				$entityDataString .= (($item->getData($attributeValue->getAttributeCode())) . '|');
			}
			$headerDone = true;
			$entityDataString .= "\n";
		}

		$finalData = ($header . "\n" . $entityDataString);

		$myfile = fopen($directoryExport . $websiteCode . "_" . $currentDate . "_" . $keyType . ".csv", "w") or die("Unable to open file!");
		fwrite($myfile, $finalData);
		fclose($myfile);

		if ($keyType == 'product') {
			generateGroupPricesFile($directoryExport, $websiteId, $websiteCode, $currentDate, $collection);
			generateStockFile($directoryExport, $websiteId, $websiteCode, $currentDate, $collection, $readConnection);
		}
	}
}

function generateGroupPricesFile($directoryExport, $websiteId, $websiteCode, $currentDate, $collection)
{
	$dataString = '';
	$header = 'entity_id|sku|price_id|website_id|all_groups|cust_group|price|website_price' . "\n";
	foreach ($collection as $entity) {
		$product = Mage::getModel('catalog/product')->load($entity->getId());
		$groupPriceArray = $product->getData('group_price');
		if (!$groupPriceArray) {
			continue;
		}
		foreach ($groupPriceArray as $groupPrice) {
			$dataString .= ($entity->getId() . '|' . $entity->getSku() . '|');
			foreach ($groupPrice as $keyItem => $item) {
				if ($keyItem == 'website_id' && $item != $websiteId) {
					continue;
				}
				$dataString .= ($item . '|');
			}
			$dataString .= "\n";
		}
	}

	$finalData = ($header . $dataString);

	$myfile = fopen($directoryExport . $websiteCode . "_" . $currentDate . "_group_price.csv", "w") or die("Unable to open file!");
	fwrite($myfile, $finalData);
	fclose($myfile);
}

function generateStockFile($directoryExport, $websiteId, $websiteCode, $currentDate, $collection, $readConnection)
{
	$dataString = '';

	$header = 'entity_id|sku|qty' . "\n";
	foreach ($collection as $entity) {
		$entityId = $entity->getId();
		$query = "SELECT cpe.entity_id, cpe.sku, csi.qty
FROM catalog_product_entity cpe
JOIN catalog_product_website cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = $websiteId
JOIN cataloginventory_stock_item csi ON csi.product_id = cpe.entity_id
WHERE cpe.entity_id = $entityId AND manage_stock = 1";
		$stockCollection = $readConnection->fetchAll($query);
		foreach ($stockCollection as $stock) {
			$dataString .= ($stock['entity_id'] . '|' . $stock['sku'] . '|' . $stock['qty'] . "\n");
		}
	}

	$finalData = ($header . $dataString);

	$myfile = fopen($directoryExport . $websiteCode . "_" . $currentDate . "_stock.csv", "w") or die("Unable to open file!");
	fwrite($myfile, $finalData);
	fclose($myfile);
}