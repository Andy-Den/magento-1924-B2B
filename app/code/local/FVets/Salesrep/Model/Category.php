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
 * Sales Rep category model
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Model_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Douglas Borella Ianitsky
     */
    protected function _construct()
    {
        $this->_init('fvets_salesrep/category');
    }

    /**
     * Save data for sales rep-category relation
     *
     * @access public
     * @param  FVets_Salesrep_Model_Salesrep $salesrep
     * @return FVets_Salesrep_Model_Category
     * @author Douglas Borella Ianitsky
     */
    public function saveSalesrepRelation($salesrep)
    {
        $data = $salesrep->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveSalesrepRelation($salesrep, $data);
        }
        return $this;
    }

    /**
     * get categories for sales rep
     *
     * @access public
     * @param FVets_Salesrep_Model_Salesrep $salesrep
     * @return FVets_Salesrep_Model_Resource_Category_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getCategoryCollection($salesrep, $filter_active = true)
    {
        $collection = Mage::getResourceModel('fvets_salesrep/category_collection')
			->addAttributeToSelect('name')
            ->addSalesrepFilter($salesrep);
        if ($filter_active)
        {
            $collection ->addAttributeToSelect('is_active')
			->addAttributeToFilter('is_active', '1');
        }
        return $collection;
    }
}
