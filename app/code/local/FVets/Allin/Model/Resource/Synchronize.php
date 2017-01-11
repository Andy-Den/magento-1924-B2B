<?php

class FVets_Allin_Model_Resource_Synchronize extends Mage_Core_Model_Resource_Db_Abstract
{

    public function _construct()
    {
        $this->_init('fvets_allin/synchronize', 'id');
    }

}
