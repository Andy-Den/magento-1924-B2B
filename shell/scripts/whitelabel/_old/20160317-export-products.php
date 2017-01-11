<?php
require_once './configScript.php';

$outputArray = array();
$outputArray[] = array('type_id', 'sku', 'group_ids', 'name', 'status', 'visibility', 'product_id');
$ignoreIds = array();

$stores = array(1,0,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,21,22,23,24,25,26);

foreach ($stores as $store)
{
	echo 'Store: ' . $store . "\n";

	Mage::app()->setCurrentStore($store);

	$products = Mage::getModel('catalog/product')->getCollection()
		->addAttributeToSelect('name')
		->addAttributeToSelect('sku')
		->addAttributeToSelect('type_id')
		->addAttributeToSelect('status')
		->addAttributeToSelect('visibility')
		->addAttributeToFilter('entity_id', array('nin' => array_keys($outputArray)))
		->setOrder('type_id', 'asc');

	echo 'Encontrados ' . $products->count() . ' produtos!' . "\n\n";

	foreach ($products as $product) {
		if (in_array($product->getId(), $ignoreIds)) {
			continue;
		}

		$localArray = array();
		echo 'Product ' . $product->getName() . ' (' . $product->getSku() . ') ' . ' [' . $product->getTypeId() . ']' . "\n";

		$localArray[0] = $product->getTypeId();
		$localArray[1] = $product->getSku();
		$localArray[2] = array();
		$localArray[3] = $product->getName();
		$localArray[4] = $product->getStatus();
		$localArray[5] = $product->getVisibility();
		$localArray[6] = $product->getId();

		if ($product->getTypeId() == 'grouped') {
			$childs = $product->getTypeInstance(true)->getAssociatedProducts($product);

			if (count($childs) > 0) {
				foreach ($childs as $child) {
					$localArray[2][] = $child->getSku();

					echo 'Product ' . $child->getName() . ' (' . $child->getSku() . ') ' . ' [' . $child->getTypeId() . ']' . "\n";

					$outputArray[$child->getId()] = array($child->getTypeId(), $child->getSku(), '', $child->getName(), $child->getStatus(), $child->getVisibility(), $child->getId());
					$ignoreIds[] = $child->getId();
				}
			}
		}

		$localArray[2] = implode(',', $localArray[2]);

		$outputArray[$product->getId()] = $localArray;
	}
}

$fp = fopen('./extras/'.date('Ymd').'-export-products.csv', 'w');

foreach ($outputArray as $fields) {
	fputcsv($fp, $fields);
}

fclose($fp);