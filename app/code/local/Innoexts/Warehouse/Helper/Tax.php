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
 * Tax helper
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */

class Innoexts_Warehouse_Helper_Tax 
    extends Innoexts_Core_Helper_Tax 
{
    /**
     * Product tax classes
     *
     * @var array of Mage_Tax_Model_Class
     */
    protected $_productTaxClasses;
    /**
     * Get tax class
     * 
     * @return Mage_Tax_Model_Class
     */
    public function getTaxClass()
    {
        return Mage::getModel('tax/class');
    }
    /**
     * Get tax class collection
     * 
     * @return Mage_Tax_Model_Mysql4_Class_Collection
     */
    public function getTaxClassCollection()
    {
        return $this->getTaxClass()
            ->getCollection();
    }
    /**
     * Get product tax class collection
     * 
     * @return Mage_Tax_Model_Mysql4_Class_Collection
     */
    public function getProductTaxClassCollection()
    {
        return $this->getTaxClassCollection()
            ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT);
    }
    /**
     * Get product tax classes
     * 
     * @return array of Mage_Tax_Model_Class
     */
    public function getProductTaxClasses()
    {
        if (is_null($this->_productTaxClasses)) {
            $taxClasses = array();
            foreach ($this->getProductTaxClassCollection() as $taxClass) {
                $taxClasses[(int) $taxClass->getClassId()] = $taxClass;
            }
            $this->_productTaxClasses = $taxClasses;
        }
        return $this->_productTaxClasses;
    }
    /**
     * Get product tax class ids
     * 
     * @return array
     */
    public function getProductTaxClassIds()
    {
        return array_keys($this->getProductTaxClasses());
    }
    /**
     * Get product tax class by id
     * 
     * @param int $taxClassId
     * 
     * @return Mage_Tax_Model_Class
     */
    public function getProductTaxClassById($taxClassId)
    {
        $taxClasses = $this->getProductTaxClasses();
        if (isset($taxClasses[$taxClassId])) {
            return $taxClasses[$taxClassId];
        } else {
            return null;
        }
    }
}