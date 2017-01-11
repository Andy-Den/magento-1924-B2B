<?php
/**
 * Customer helper
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Ianitsky
 */
class FVets_Salesrep_Helper_Customer extends FVets_Salesrep_Helper_Data
{
	function getAllowedCategories($customer = null, $cache = true, $activeFilter = true)
	{
		if ((!$allowedCategories = Mage::registry('allowed_categories')) || !$cache)
		{
			if (!$customer) {
				$customer = Mage::getSingleton('customer/session')->getCustomer();
			}
			$allowedCategories = array();
			$unalloyedCategories = explode(',', $customer->getRestrictedBrands());
			foreach (explode(',', $customer->getFvetsSalesrep()) as $salesrep)
			{
				foreach(Mage::getModel('fvets_salesrep/category')->getCategoryCollection($salesrep, $activeFilter) as $category)
				{
					if (!in_array($category->getId(), $unalloyedCategories))
					{
						$allowedCategories[] = $category;
					}
				}
			}

			@Mage::unregister('allowed_categories');
			Mage::register('allowed_categories', $allowedCategories);
			Mage::getSingleton('customer/session')->setAllowedCategories($allowedCategories);
		}

		return $allowedCategories;
	}

	public function getCustomerSalesreps()
	{
		$return = array();
		foreach (explode(',', Mage::getSingleton('customer/session')->getCustomer()->getFvetsSalesrep()) as $salesrep)
		{
			$return[] = Mage::getModel('fvets_salesrep/salesrep')->load($salesrep);
		}
		return $return;
	}
}