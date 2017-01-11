<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * AttributeMenu model
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Model_Attributemenu extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'fvets_attributemenu_attributemenu';
    const CACHE_TAG = 'fvets_attributemenu_attributemenu';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'fvets_attributemenu_attributemenu';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'attributemenu';

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('fvets_attributemenu/attributemenu');
    }

    /**
     * before save attributemenu
     *
     * @access protected
     * @return FVets_AttributeMenu_Model_Attributemenu
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save attributemenu relation
     *
     * @access public
     * @return FVets_AttributeMenu_Model_Attributemenu
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
    /**
      * get Value
      *
      * @access public
      * @return array
      * @author Ultimate Module Creator
      */
    public function getValue()
    {
        if (!$this->getData('value')) {
            return explode(',', $this->getData('value'));
        }
        return $this->getData('value');
    }
}
