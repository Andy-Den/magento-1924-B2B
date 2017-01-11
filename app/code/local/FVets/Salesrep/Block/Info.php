<?php

class FVets_Salesrep_Block_Info extends Mage_Core_Block_Template
{

	protected $_customer = null;

    public function _construct()
    {
        $this->setData(array(
            'cache_lifetime' => 86400,
            'cache_tags' => Mage_Customer_Model_Customer::CACHE_TAG,
            //'template' => 'fvets/salesrep/info.phtml',
        ));

        return parent::_construct();
    }

    public function getCacheKeyInfo()
    {
        return array(
            'fvets_salesrep_block_info',
            Mage::app()->getStore()->getCode(),
            $this->getCustomer()->getId(),
			$this->getNameInLayout()
        );
    }

    public function getSalesrep()
    {
		$ids = explode(',',$this->getCustomer()->getFvetsSalesrep());
        return Mage::getModel('fvets_salesrep/salesrep')->load(array_shift($ids));
    }

	public function getCollection()
	{
		/*$collection = Mage::getResourceModel('fvets_salesrep/salesrep_collection')
			->addFieldToFilter('id', array('in' => explode(',',$this->getCustomer()->getFvetsSalesrep())))
			->addStoresToFilter(Mage::app()->getStore()->getId());
		return $collection;*/

		return Mage::getModel('fvets_salesrep/salesrep')->getCustomerReps();
	}

	public  function setCustomer($customer_id) {
		if ($customer_id > 0)
			$this->_customer = Mage::getModel('customer/customer')->load($customer_id);
	}

    public function getCustomer()
    {
			if (!isset($this->_customer))
				$this->_customer = Mage::getSingleton('customer/session')->getCustomer();

			return $this->_customer;
    }
    
    public function getImageUrl($salesrep)
    {
        return $this->helper('fvets_salesrep')->getImageUrl($salesrep);
    }

}
