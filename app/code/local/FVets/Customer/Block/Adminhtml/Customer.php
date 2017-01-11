<?php

class FVets_Customer_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Customer
{

    public function __construct()
    {
        parent::__construct();
        if (!Mage::getSingleton('admin/session')->isAllowed('customer/create')) {
            $this->_removeButton('add');
        }
    }

}