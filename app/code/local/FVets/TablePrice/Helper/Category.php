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
 * Category helper
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Helper_Category extends FVets_TablePrice_Helper_Data
{

    /**
     * get the selected tables prices for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author Douglas Ianitsky
     */
    public function getSelectedTablesprices(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedTablesprices()) {
            $tablesprices = array();
            foreach ($this->getSelectedTablespricesCollection($category) as $tableprice) {
                $tablesprices[] = $tableprice;
            }
            $category->setSelectedTablesprices($tablesprices);
        }
        return $category->getData('selected_tablesprices');
    }

    /**
     * get table price collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return FVets_TablePrice_Model_Resource_Tableprice_Collection
     * @author Douglas Ianitsky
     */
    public function getSelectedTablespricesCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('fvets_tableprice/tableprice_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}
