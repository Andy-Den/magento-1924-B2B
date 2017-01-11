<?php

class FVets_Customer_Model_Customer_Attribute_Source_Group extends Mage_Customer_Model_Customer_Attribute_Source_Group
{
    public function getAllOptions()
    {
        $customer = Mage::registry('current_customer');
        if (Mage::getStoreConfig('validate_customer/general/remove_default_customer_groups_from_presentation', $customer->getStoreId())) {
            return $this->_getAllGroupFilteredOptions();
        } else {
            return $this->_getAllGroupNonFilteredOptions();
        }
    }

    public function _getAllGroupFilteredOptions()
    {
        if (!$this->_options) {
            $customer = Mage::registry('current_customer');
            $this->_options = Mage::getResourceModel('customer/group_collection')
                ->setRealGroupsFilter();

            if ($customer && $customer->getId()) {
                $this->_options->addWebsiteFilter($customer->getWebsiteId());
            }

            //não trazer grupo default
            $this->_options->addFieldToFilter('customer_group_code', array('neq' => 'General'));

            $this->_options->load();
            $this->_options = $this->_options->toOptionArray();

            //caso hajam mais grupos de clientes(tabelas de preço) além da "default da distribuidora", remover essa default;
            if (count($this->_options) > 1) {
                $websiteCode = Mage::getModel('core/website')->load($customer->getWebsiteId())->getCode();

                foreach ($this->_options as $key => $item) {
                    if (strtoupper($item['label']) == strtoupper($websiteCode)) {
                        unset($this->_options[$key]);
                    }
                }
            }
        }
        return $this->_options;
    }

    public function _getAllGroupNonFilteredOptions()
    {
        if (!$this->_options) {
            $customer = Mage::registry('current_customer');
            $this->_options = Mage::getResourceModel('customer/group_collection')
                ->setRealGroupsFilter();

            if ($customer && $customer->getId()) {
                $this->_options->addWebsiteFilter($customer->getWebsiteId());
            }

            $this->_options->load();
            $this->_options = $this->_options->toOptionArray();
        }
        return $this->_options;
    }
}