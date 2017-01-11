<?php

require_once './configScript.php';

$products = Mage::getModel('catalog/product')->getCollection()->addWebsiteFilter($websiteId);
$storeviewIds = Mage::getModel('core/website')->load($websiteId)->getStoreIds();

if (($handle = fopen("extras/20160328-retirar_visibilidade_produtos.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		foreach ($storeviewIds as $storeviewId) {
			Mage::app()->setCurrentStore($storeviewId);

			$products = Mage::getModel('catalog/product')
				->getCollection()
				->addWebsiteFilter($websiteId)
				->addAttributeToFilter('id_erp', $data[0]);

			foreach ($products as $product) {
				if ($product->getSku()) {
					$product->setStoreId($storeviewId);
					echo "Store: " . $storeviewId . "\n";
					echo "Updating " . $product->getIdErp() . "\n";
					$product->setStatus(2);
					$product->setVisibility(1);
					$product->getResource()->saveAttribute($product, 'status');
					$product->getResource()->saveAttribute($product, 'visibility');
				} else {
					echo "-" . "\n";
				}
			}
		}
	}
}