<?php
/**
 * FVets_Salesrep extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Category helper
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Helper_Category extends FVets_Salesrep_Helper_Data
{

    /**
     * get the selected sales representatives for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedSalesreps(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedSalesreps()) {
            $salesreps = array();
            foreach ($this->getSelectedSalesrepsCollection($category) as $salesrep) {
                $salesreps[] = $salesrep;
            }
            $category->setSelectedSalesreps($salesreps);
        }
        return $category->getData('selected_salesreps');
    }

    /**
     * get sales rep collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return FVets_Salesrep_Model_Resource_Salesrep_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedSalesrepsCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('fvets_salesrep/salesrep_collection')
            ->addCategoryFilter($category);
        return $collection;
    }

	public function getRepBrands($salesrep, $filterByStore = false)
	{
		if (!$salesrep->hasSelectedCategories())
		{
			$categories = array();
			$restrictedBrands = Mage::getSingleton('customer/session')->getCustomer()->getRestrictedBrands();
			$restrictedBrands = explode(',', $restrictedBrands);
			if ($filterByStore) {
				$categoryCollection = $salesrep->getSelectedCategoriesCollection(true, Mage::app()->getStore()->getId());
			} else {
				$categoryCollection = $salesrep->getSelectedCategoriesCollection();
			}

			foreach ($categoryCollection as $category)
			{
				if ($category->getIsActive() && !in_array($category->getId(), $restrictedBrands))
				{
					$categories[] = $category;
				}
			}
			$salesrep->setSelectedCategories($categories);
		}
		return $salesrep->getData('selected_categories');
	}
}
