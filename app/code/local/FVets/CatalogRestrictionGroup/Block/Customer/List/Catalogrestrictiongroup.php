<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group list on customer page block
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Customer_List_Catalogrestrictiongroup extends Mage_Customer_Block_Customer_Abstract
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
            $customer = Mage::registry('customer');
            $collection = Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_collection')
                ->addFieldToFilter('status', 1)
                ->addCustomerFilter($customer);
            $collection->getSelect()->order('related_customer.position', 'ASC');
            $this->setData('catalogrestrictiongroup_collection', $collection);
        }
        return $this->getData('catalogrestrictiongroup_collection');
    }
}
