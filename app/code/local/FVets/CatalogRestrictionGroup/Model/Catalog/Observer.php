<?php

class FVets_CatalogRestrictionGroup_Model_Catalog_Observer
{
	function removeUnavailableProducts($observer)
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$collection = $observer->getCollection();

			$subquery = new Zend_Db_Expr('SELECT
											fcrgep.product_id
										FROM
											'.$collection->getTable('fvets_catalogrestrictiongroup/entity').' AS fcrge,
											'.$collection->getTable('fvets_catalogrestrictiongroup/entity_product').' AS fcrgep,
											'.$collection->getTable('fvets_catalogrestrictiongroup/entity_customer').' AS fcrgec
										WHERE
											fcrge.status = 1
											AND fcrgec.customer_id = '.Mage::getSingleton('customer/session')->getCustomer()->getId().'
											AND fcrge.entity_id = fcrgec.catalogrestrictiongroup_id
											AND fcrge.entity_id = fcrgep.catalogrestrictiongroup_id');

			$collection->getSelect()->where('e.entity_id NOT IN (?)', $subquery);
		}
	}
}