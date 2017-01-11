<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Product helper
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Helper_Product extends FVets_CatalogRestrictionGroup_Helper_Data
{

    /**
     * get the selected restriction groups for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return array()
     * @author Douglas Ianitsky
     */
    public function getSelectedCatalogrestrictiongroups(Mage_Catalog_Model_Product $product)
    {
        if (!$product->hasSelectedCatalogrestrictiongroups()) {
            $catalogrestrictiongroups = array();
            foreach ($this->getSelectedCatalogrestrictiongroupsCollection($product) as $catalogrestrictiongroup) {
                $catalogrestrictiongroups[] = $catalogrestrictiongroup;
            }
            $product->setSelectedCatalogrestrictiongroups($catalogrestrictiongroups);
        }
        return $product->getData('selected_catalogrestrictiongroups');
    }

    /**
     * get restriction group collection for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Collection
     * @author Douglas Ianitsky
     */
    public function getSelectedCatalogrestrictiongroupsCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_collection')
            ->addProductFilter($product);
        return $collection;
    }
}
