<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group customer model
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup_Customer extends Mage_Core_Model_Abstract
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
        $this->_init('fvets_catalogrestrictiongroup/catalogrestrictiongroup_customer');
    }

    /**
     * Save data for restriction group-customer relation
     * @access public
     * @param  FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup $catalogrestrictiongroup
     * @return FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup_Customer
     * @author Douglas Ianitsky
     */
    public function saveCatalogrestrictiongroupRelation($catalogrestrictiongroup)
    {
        $data = $catalogrestrictiongroup->getCustomersData();
        if (!is_null($data)) {
            $this->_getResource()->saveCatalogrestrictiongroupRelation($catalogrestrictiongroup, $data);
        }
        return $this;
    }

    /**
     * get customers for restriction group
     *
     * @access public
     * @param FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup $catalogrestrictiongroup
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Customer_Collection
     * @author Douglas Ianitsky
     */
    public function getCustomerCollection($catalogrestrictiongroup)
    {
        $collection = Mage::getResourceModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup_customer_collection')
            ->addCatalogrestrictiongroupFilter($catalogrestrictiongroup);
        return $collection;
    }
}
