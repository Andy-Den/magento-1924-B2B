<?php

class FVets_Allin_Model_Resource_Synchronize_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        $this->_init('fvets_allin/synchronize');
    }
}