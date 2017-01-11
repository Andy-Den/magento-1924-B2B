<?php
/**
 * Innoexts
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Shipping helper
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Helper_Warehouse_Assignment_Method 
    extends Mage_Core_Helper_Abstract 
{
    /**
     * Single methods
     * 
     * @var array of Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Single_Abstract
     */
    protected $_singleMethods;
    /**
     * Multiple methods
     * 
     * @var array of Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract
     */
    protected $_multipleMethods;
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    public function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get single methods
     * 
     * @return array of Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Single_Abstract
     */
    public function getSingleMethods()
    {
        if (is_null($this->_singleMethods)) {
            $helper     = $this->getWarehouseHelper();
            $methods    = array();
            $config     = Mage::getStoreConfig('single_assignment_methods');
            foreach ($config as $code => $methodConfig) {
                if (!isset($methodConfig['model'])) {
                    Mage::throwException($helper->__('Invalid model for single assignment method: %', $code));
                }
                $modelName = $methodConfig['model'];
                try {
                    $method = Mage::getModel($modelName, $methodConfig);
                } catch (Exception $e) {
                    Mage::logException($e);
                    return false;
                }
                $method->setId($code);
                $methods[$code] = $method;
            }
            $this->_singleMethods = $methods;
        }
        return $this->_singleMethods;
    }
    /**
     * Get single method
     * 
     * @param string $code
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Single_Abstract
     */
    public function getSingleMethod($code)
    {
        $methods = $this->getSingleMethods();
        if (isset($methods[$code])) {
            return $methods[$code];
        } else {
            return null;
        }
    }
    /**
     * Get current single method
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Single_Abstract
     */
    public function getCurrentSingleMethod()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        return $this->getSingleMethod($config->getSingleAssignmentMethodCode());
    }
    /**
     * Get multiple methods
     * 
     * @return array of Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract
     */
    public function getMultipleMethods()
    {
        if (is_null($this->_multipleMethods)) {
            $helper     = $this->getWarehouseHelper();
            $methods    = array();
            $config     = Mage::getStoreConfig('multiple_assignment_methods');
            foreach ($config as $code => $methodConfig) {
                if (!isset($methodConfig['model'])) {
                    Mage::throwException($helper->__('Invalid model for multiple assignment method: %', $code));
                }
                $modelName = $methodConfig['model'];
                try {
                    $method = Mage::getModel($modelName, $methodConfig);
                } catch (Exception $e) {
                    Mage::logException($e);
                    return false;
                }
                $method->setId($code);
                $methods[$code] = $method;
            }
            $this->_multipleMethods = $methods;
        }
        return $this->_multipleMethods;
    }
    /**
     * Get multiple method
     * 
     * @param string $code
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract
     */
    public function getMultipleMethod($code)
    {
        $methods = $this->getMultipleMethods();
        if (isset($methods[$code])) {
            return $methods[$code];
        } else {
            return null;
        }
    }
    /**
     * Get current multiple method
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Multiple_Abstract
     */
    public function getCurrentMultipleMethod()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        return $this->getMultipleMethod($config->getMultipleAssignmentMethodCode());
    }
    /**
     * Get current method
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Assignment_Method_Abstract
     */
    public function getCurrentMethod()
    {
        $config = $this->getWarehouseHelper()->getConfig();
        if ($config->isMultipleMode()) {
            return $this->getCurrentMultipleMethod();
        } else {
            return $this->getCurrentSingleMethod();
        }
    }
    /**
     * Apply quote stock items
     * 
     * @param Innoexts_Warehouse_Model_Sales_Quote $quote
     * 
     * @return Innoexts_Warehouse_Helper_Warehouse_Assignment_Method
     */
    public function applyQuoteStockItems($quote)
    {
        $method = $this->getCurrentMethod();
        if (!$method) {
            return $this;
        }
        $method->setQuote($quote)->applyQuoteStockItems();
        return $this;
    }
    /**
     * Get quote stock identifier
     * 
     * @param Innoexts_Warehouse_Model_Sales_Quote $quote
     * 
     * @return int|null
     */
    public function getQuoteStockId($quote = null)
    {
        $method     = $this->getCurrentMethod();
        if (!$method) {
            return null;
        }
        return $method->setQuote($quote)->getStockId();
    }
    /**
     * Get product stock identifier
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return int
     */
    public function getProductStockId($product)
    {
        $method = $this->getCurrentMethod();
        if (!$method) {
            return null;
        }
        return $method->getProductStockId($product);
    }
}