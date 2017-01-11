<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/9/15
 * Time: 10:19 AM
 */

require_once './configScript.php';


	$collection = Mage::getModel('review/review')->getCollection()
		->addFieldToFilter('status_id', Mage_Review_Model_Review::STATUS_APPROVED)
		->addStoreData()
	;

foreach ($collection as $object)
{
	$stores = $object->getStores();
	if (!empty($stores)) {

		$insertedStoreIds = array();
		foreach ($stores as $storeId)
		{
			if (in_array($storeId, $insertedStoreIds))
			{
				continue;
			}

			$insertedStoreIds[] = $storeId;

			$product = Mage::getModel('catalog/product');
			$product->setStoreId($storeId);
			$product->load($object->getEntityPkValue());
			$product->setHasReview('1');
			$product->getResource()->saveAttribute($product, 'has_review');

			echo '+';
		}
		echo "|";
	}
}


echo "\n";
echo 'Bye';