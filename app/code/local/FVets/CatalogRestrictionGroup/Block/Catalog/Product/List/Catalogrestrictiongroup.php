<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group list on product page block
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Catalog_Product_List_Catalogrestrictiongroup extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * get the list of restriction groups
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Collection
     * @author Douglas Ianitsky
     */
    public function getCatalogrestrictiongroupCollection()
    {
        if (!$this->hasData('catalogrestrictiongroup_collection')) {
            $product = Mage::registry('product');
            $collection = Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_collection')
                ->addFieldToFilter('status', 1)
                ->addProductFilter($product);
            $collection->getSelect()->order('related_product.position', 'ASC');
            $this->setData('catalogrestrictiongroup_collection', $collection);
        }
        return $this->getData('catalogrestrictiongroup_collection');
    }
}
