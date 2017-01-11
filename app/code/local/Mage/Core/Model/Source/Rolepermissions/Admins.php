<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Mage_Core_Model_Source_Rolepermissions_Admins extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options = null;

    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options))
        {
            $this->_options = array();

            $adminsArray = Mage::getModel('admin/user')
                ->getCollection()
                ->load()
                ->toArray();

            foreach ($adminsArray['items'] as $admin)
            {
                $this->_options[] = array(
                    'label' => $admin['username'],
                    'value' => $admin['user_id']
                );
            }
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array('value'=>'', 'label'=>''));
        }

        return $options;
    }
}
