<?php

class FVets_CatalogRestrictionGroup_Model_Source_Restrictiongroup extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    public function getAllOptions()
    {
        if ($this->_options === null)
        {
            $param = (Mage::app()->getRequest()->getParam('id')) ? Mage::app()->getRequest()->getParam('id') : Mage::app()->getRequest()->getParam('customer_id') ;
            $customer =  Mage::getModel("customer/customer")->load($param);

            $collection = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup')
                ->getCollection()
                ->addFieldToFilter('website_id', $customer->getWebsiteId())
                ->load()
            ;
            $this->_options = $collection
                ->toOptionArray();
        }

        return $this->_options;
    }
}