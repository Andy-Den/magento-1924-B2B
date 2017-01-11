<?php

class FVets_Salesrep_Model_Resource_Salesrep extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     */
    public function _construct()
    {
        $this->_init('fvets_salesrep/salesrep', 'id');
    }
}
