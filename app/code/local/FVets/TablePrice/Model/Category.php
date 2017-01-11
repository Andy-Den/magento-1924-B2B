<?php
/**
 * FVets_TablePrice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Table Price category model
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Model_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Douglas Ianitsky
     */
    protected function _construct()
    {
        $this->_init('fvets_tableprice/category');
    }

    /**
     * Save data for table price-category relation
     *
     * @access public
     * @param  FVets_TablePrice_Model_Tableprice $tableprice
     * @return FVets_TablePrice_Model_Category
     * @author Douglas Ianitsky
     */
    public function saveTablepriceRelation($tableprice)
    {
        $data = $tableprice->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveTablepriceRelation($tableprice, $data);
        }
        return $this;
    }

    /**
     * get categories for table price
     *
     * @access public
     * @param FVets_TablePrice_Model_Tableprice $tableprice
     * @return FVets_TablePrice_Model_Resource_Category_Collection
     * @author Douglas Ianitsky
     */
    public function getCategoryCollection($tableprice)
    {
        $collection = Mage::getResourceModel('fvets_tableprice/category_collection')
            ->addTablepriceFilter($tableprice);
        return $collection;
    }
}
