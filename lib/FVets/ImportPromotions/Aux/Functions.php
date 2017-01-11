<?php

class Aux_Functions
{
	function getProductByIdErp($idErp, $websiteId)
	{

		$stores = Mage::getModel('core/website')->load($websiteId)->getStoreCollection();

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
				return $product->setStoreId($store->getId())->load($product->getId());
			}
		}
		return null;
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

	function getStoreviewsByWebsiteCode($websiteCode)
	{
		$website = Mage::getModel('core/website')->getCollection()
			->addFieldToFilter('code', $websiteCode)
			->fetchOne();
		return $website->getStoreCollection();
	}

	function getWebsiteByCode($websiteCode)
	{
		return Mage::getModel('core/website')->getCollection()
			->addFieldToFilter('code', $websiteCode)
			->getFirstItem();
	}

	function getTranslatedFromTo($fieldName)
	{
		if (isset(Aux_Constants::$fieldsTranslation[$fieldName]))
		{
			return Aux_Constants::$fieldsTranslation[$fieldName];
		}
		return null;
	}

	function getTranslatedToFrom($fieldName)
	{
		foreach (Aux_Constants::$fieldsTranslation as $key => $field)
		{
			if ($field == $fieldName)
			{
				return $key;
				break;
			}
		}
		return null;
	}

	function getIdsErpForRuleName($idsErp)
	{
		$idsErpArray = explode(',', $idsErp);
		if (count($idsErpArray) < 20)
		{
			return $idsErp;
		}
		return 'manyIds (' . implode(',', array_slice($idsErpArray, 0, 20)) . '...)';
	}
}