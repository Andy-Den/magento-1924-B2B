<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Customer helper
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Helper_Customer extends FVets_CatalogRestrictionGroup_Helper_Data
{

    /**
     * get the selected restriction groups for a customer
     *
     * @access public
     * @param Mage_Customer_Model_Customer $customer
     * @return array()
     * @author Douglas Ianitsky
     */
    public function getSelectedCatalogrestrictiongroups(Mage_Customer_Model_Customer $customer)
    {
        if (!$customer->hasSelectedCatalogrestrictiongroups()) {
            $catalogrestrictiongroups = array();
            foreach ($this->getSelectedCatalogrestrictiongroupsCollection($customer) as $catalogrestrictiongroup) {
                $catalogrestrictiongroups[] = $catalogrestrictiongroup;
            }
            $customer->setSelectedCatalogrestrictiongroups($catalogrestrictiongroups);
        }
        return $customer->getData('selected_catalogrestrictiongroups');
    }

    /**
     * get restriction group collection for a customer
     *
     * @access public
     * @param Mage_Customer_Model_Customer $customer
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Collection
     * @author Douglas Ianitsky
     */
    public function getSelectedCatalogrestrictiongroupsCollection(Mage_Customer_Model_Customer $customer)
    {
        $collection = Mage::getResourceModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup_collection')
            ->addCustomerFilter($customer);
        return $collection;
    }
}
