<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Model_Resource_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('amrolepermissions/rule', 'rule_id');
    }
}
