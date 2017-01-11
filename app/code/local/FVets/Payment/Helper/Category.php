<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Category helper
 *
 * @category    FVets
 * @package     FVets_Payment
 * @author      Douglas Borella Ianitsky
 */
class FVets_Payment_Helper_Category extends FVets_Payment_Helper_Data
{

	protected $_brands;

    /**
     * get the selected conditions for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedConditions(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedConditions()) {
            $conditions = array();
            foreach ($this->getSelectedConditionsCollection($category) as $condition) {
                $conditions[] = $condition;
            }
            $category->setSelectedConditions($conditions);
        }
        return $category->getData('selected_conditions');
    }

    /**
     * get condition collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return FVets_Payment_Model_Resource_Condition_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedConditionsCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('fvets_payment/condition_collection')
            ->addCategoryFilter($category);
        return $collection;
    }

	/**
	 * get quote categories
	 * @return array()
	 * @author Douglas Borella Ianitsky
	 */
	public function getQuoteCategories()
	{
		if (!$this->_brands)
		{
			$this->_brands = array();
			foreach (Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems() as $item)
			{
				$rootCategory = Mage::getModel('catalog/category')
					->load(Mage::app()->getStore()->getRootCategoryId());

				$category = null;

				if ($rootCategory->getLevel() != 2)
				{
					$category = Mage::getModel('catalog/category')
						->getCollection()
						->addAttributeToFilter('entity_id', array('in' => $item->getProduct()->getCategoryIds()))
						->addAttributeToFilter('level', '2')
						->addFieldToFilter('path', array('like' => $rootCategory->getPath() . '/%'))
						->getFirstItem();
				}
				else
				{
					 $category = $rootCategory;
				}

				$this->_brands[$category->getId()] = $category;
			}
		}
		return $this->_brands;
	}
}
