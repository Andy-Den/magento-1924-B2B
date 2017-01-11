<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group product model
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup_Product extends Mage_Core_Model_Abstract
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
        $this->_init('fvets_catalogrestrictiongroup/catalogrestrictiongroup_product');
    }

    /**
     * Save data for restriction group-product relation
     * @access public
     * @param  FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup $catalogrestrictiongroup
     * @return FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup_Product
     * @author Douglas Ianitsky
     */
    public function saveCatalogrestrictiongroupRelation($catalogrestrictiongroup)
    {
        $data = $catalogrestrictiongroup->getProductsData();
        if (!is_null($data)) {
            $this->_getResource()->saveCatalogrestrictiongroupRelation($catalogrestrictiongroup, $data);
        }
        return $this;
    }

    /**
     * get products for restriction group
     *
     * @access public
     * @param FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup $catalogrestrictiongroup
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Product_Collection
     * @author Douglas Ianitsky
     */
    public function getProductCollection($catalogrestrictiongroup)
    {
        $collection = Mage::getResourceModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup_product_collection')
            ->addCatalogrestrictiongroupFilter($catalogrestrictiongroup);
        return $collection;
    }
}
