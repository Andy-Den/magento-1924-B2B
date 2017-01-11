<?php

class TM_Easyslide_Model_Easyslide_Slides extends Mage_Core_Model_Abstract
{
    const TARGET_SELF  = 0;
    const TARGET_BLANK = 1;
    const TARGET_POPUP = 2;

    public function _construct()
    {
        parent::_construct();
        $this->_init('easyslide/easyslide_slides');
    }

    public function getTargetModes()
    {
        return array(
            self::TARGET_SELF  => Mage::helper('easyslide')->__('Same window'),
            self::TARGET_BLANK => Mage::helper('easyslide')->__('New window'),
            self::TARGET_POPUP => Mage::helper('easyslide')->__('Popup')
        );
    }
}