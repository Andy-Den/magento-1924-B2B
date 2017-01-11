<?php

class FVets_Salesrep_Model_Catalog_Observer
{
	function removeUnavailableCategories($observer)
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn() && Mage::getStoreConfig('fvets_salesrep/catalog/product_filter'))
		{
			$collection = $observer->getCategoryCollection();

			if (!$collection instanceof FVets_Salesrep_Model_Resource_Category_Collection)
			{
				if(!Mage::registry('allowAllCategories')) //Permitir todas as categorias
				{
					$allowedCategories = array();

					 foreach (Mage::helper('fvets_salesrep/customer')->getAllowedCategories() as $category)
					 {
						 $allowedCategories[]['like'] = $category->getPath();
						 $allowedCategories[]['like'] = $category->getPath() . '/%';
					 }

					if (count($allowedCategories) > 0)
					{
						$collection->addFieldToFilter('path', $allowedCategories);
					}
					else
					{
						/**
						 * Clear collection
						 */
						$collection->getSelect()->reset(Zend_Db_Select::WHERE);
						$collection->getSelect()->where('e.entity_id < 0');
					}
				}
				else
				{
					Mage::unregister('allowAllCategories');
				}
			}
		}
	}

	function removeUnavailableProducts($observer)
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn() && Mage::getStoreConfig('fvets_salesrep/catalog/product_filter'))
		{
			$collection = $observer->getCollection();

			$allowedCategories = array();

			foreach (Mage::helper('fvets_salesrep/customer')->getAllowedCategories() as $category)
			{
				$allowedCategories[] = $category->getId();
			}

			if (count($allowedCategories) > 0)
			{
				$collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
					->addAttributeToFilter('category_id',$allowedCategories);
				$collection->getSelect()->group('e.entity_id');
			}
			else
			{
				/**
				 * Clear collection
				 */
				$collection->getSelect()->reset(Zend_Db_Select::WHERE);
				$collection->getSelect()->where('e.entity_id < 0');
			}
		}
	}
}