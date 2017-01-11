<?php

class FVets_Salesrep_Model_Source_Salesrep extends Mage_Eav_Model_Entity_Attribute_Source_Table
{

    public function getAllOptions()
    {
        if ($this->_options === null)
		{
			$param = (Mage::app()->getRequest()->getParam('id')) ? Mage::app()->getRequest()->getParam('id') : Mage::app()->getRequest()->getParam('customer_id') ;
			$customer =  Mage::getModel("customer/customer")->load($param);

            $this->_options = Mage::getResourceModel('fvets_salesrep/salesrep_collection')
				->addStoresToFilter($customer->getSharedStoreIds())
				->load()
				->toOptionArray();
        }

        return $this->_options;
    }

}
