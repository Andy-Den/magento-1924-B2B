<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Model_Resource_Website_Collection extends Mage_Core_Model_Mysql4_Website_Collection
{
    public function toOptionHash()
    {
        $hash = parent::_toOptionHash('website_id', 'name');

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getScopeStoreviews())
        {
            $accessible = $rule->getPartiallyAccessibleWebsites();

            foreach ($hash as $id => $name)
            {
                if (!in_array($id, $accessible))
                    unset($hash[$id]);
            }
        }

        return $hash;
    }
}