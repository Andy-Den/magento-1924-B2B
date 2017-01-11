<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Table rate method model
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Shippingtablerate_Tablerate_Method 
    extends Innoexts_Core_Model_Abstract 
{
    /**
     * Prefix of model events names
     * 
     * @var string
     */
    protected $_eventPrefix = 'shippingtablerate_tablerate_method';
    /**
     * Parameter name in event
     * 
     * In observe method you can use $observer->getEvent()->getItem() in this case
     * 
     * @var string
     */
    protected $_eventObject = 'tablerate_method';
    /**
     * Model cache tag for clear cache in after save and after delete
     * 
     * When you use true - all cache will be clean
     * 
     * @var string || true
     */
    protected $_cacheTag = 'shippingtablerate_tablerate_method';
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('shippingtablerate/tablerate_method');
    }
    /**
     * Get warehouse helper
     *
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get shipping table rate helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getTextHelper()
    {
        return $this->getWarehouseHelper();
    }
    /**
     * Get filters
     * 
     * @return array
     */
    protected function getFilters()
    {
        return array(
            'code'              => $this->getTextFilter(), 
            'name'              => $this->getTextFilter(), 
        );
    }
    /**
     * Get code validator
     * 
     * @return Zend_Validate
     */
    protected function getCodeValidator()
    {
        $helper         = $this->getTextHelper();
        $validator      = new Zend_Validate_Regex(array('pattern' => '/^[a-z]+[a-z0-9_]*$/'));
        $validator->setMessage($helper->__(
            'Method code may only contain letters (a-z), numbers (0-9) or underscore(_), the first character must be a letter'), 
            Zend_Validate_Regex::NOT_MATCH
        );
        return $this->getTextValidator(true, 0, 32)->addValidator($validator);
    }
    /**
     * Get validators
     * 
     * @return array
     */
    protected function getValidators()
    {
        return array(
            'code'              => $this->getCodeValidator(), 
            'name'              => $this->getTextValidator(true, 0, 128), 
        );
    }
    /**
     * Get model
     * 
     * @return Innoexts_Warehouse_Model_Shippingtablerate_Tablerate_Method
     */
    protected function _getModel()
    {
        return Mage::getModel('shippingtablerate/tablerate_method');
    }
    /**
     * Validate method
     *
     * @throws Mage_Core_Exception
     * 
     * @return bool
     */
    public function validate()
    {
        $helper = $this->getTextHelper();
        parent::validate();
        $errorMessages      = array();
        $tablerateMethod    = $this->_getModel()->loadByCode($this->getCode(), $this->getId());
        if ($tablerateMethod->getId()) {
            array_push($errorMessages, $helper->__('Method with the same code already exists.'));
        }
        $tablerateMethod    = $this->_getModel()->loadByName($this->getName(), $this->getId());
        if ($tablerateMethod->getId()) {
            array_push($errorMessages, $helper->__('Method with the same name already exists.'));
        }
        if (count($errorMessages)) {
            Mage::throwException(join("\n", $errorMessages));
        }
        return true;
    }
    /**
     * Get title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->getName();
    }
    /**
     * Processing object before delete data
     * 
     * @return Innoexts_Warehouse_Model_Shippingtablerate_Tablerate_Method
     */
    protected function _beforeDelete()
    {
        if (1 == $this->getId()) {
            $helper = $this->getTextHelper();
            Mage::throwException($helper->__('The default method can\'t be deleted.'));
        }
        parent::_beforeDelete();
        return $this;
    }
    /**
     * Load method by code
     * 
     * @param string $code
     * @param int $exclude
     * 
     * @return Innoexts_Warehouse_Model_Shippingtablerate_Tablerate_Method
     */
    public function loadByCode($code, $exclude = null)
    {
        $this->_getResource()->loadByCode($this, $code, $exclude);
        $this->setOrigData();
        return $this;
    }
    /**
     * Load method by name
     * 
     * @param string $name
     * @param int $exclude
     * 
     * @return Innoexts_Warehouse_Model_Shippingtablerate_Tablerate_Method
     */
    public function loadByName($name, $exclude = null)
    {
        $this->_getResource()->loadByName($this, $name, $exclude);
        $this->setOrigData();
        return $this;
    }
}