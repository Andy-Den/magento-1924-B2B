<?php

class FVets_Allin_Model_Resource_Account_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        $this->_init('fvets_allin/account');
    }

    public function toOptionArray()
    {
        $data = $this->_toOptionArray('id', 'name');
        array_unshift($data, array('value' => '', 'label' => Mage::helper('core')->__('Please select')));
        return $data;
    }
}
