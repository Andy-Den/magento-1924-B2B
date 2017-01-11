<?php

class MageParts_ConfirmCustomer_Model_Source_Options
{

    public function getAllOptions()
    {
        return Mage::helper('confirmcustomer')->getApprovalStates();
    }

}
