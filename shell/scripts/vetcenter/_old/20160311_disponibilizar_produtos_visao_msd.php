<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 3/11/16
 * Time: 1:48 PM
 */

require_once './configScript.php';

$category_id = 720;

$vetcenterStoreviewId = 24;
$vetcenterMsdStoreviewId = 18;

$products = Mage::getModel('catalog/category')->load($category_id)
	->getProductCollection();

foreach ($products as $product)
{
//	if ($product->getSku() == 'MSD10006')
//	{
		$product->setStoreId($vetcenterStoreviewId);
		$product->load();

		if ($product->getIdErp() && $product->getStatus() == 1 && $product->getVisibility() == 4)
		{
			$idErpTemp = $product->getIdErp();
			$product->setStoreId($vetcenterMsdStoreviewId);
			$product->load();
			$product->setIdErp($idErpTemp);
			$product->setStatus(1);
			$product->setVisibility(4);
//			$product->getResource()->saveAttribute($product, 'id_erp');
//			$product->getResource()->saveAttribute($product, 'status');
//			$product->getResource()->saveAttribute($product, 'visibility');
			$product->save();
		}
		echo $product->getSku() . "\n";
	//}
}