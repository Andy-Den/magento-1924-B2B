<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group model
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'fvets_catalogrestrictiongroup_catalogrestrictiongroup';
    const CACHE_TAG = 'fvets_catalogrestrictiongroup_catalogrestrictiongroup';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'fvets_catalogrestrictiongroup_catalogrestrictiongroup';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'catalogrestrictiongroup';
    protected $_productInstance = null;
    protected $_customerInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('fvets_catalogrestrictiongroup/catalogrestrictiongroup');
    }

    /**
     * before save restriction group
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup
     * @author Douglas Ianitsky
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
     * save restriction group relation
     *
     * @access public
     * @return FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup
     * @author Douglas Ianitsky
     */
    protected function _afterSave()
    {
        $this->getProductInstance()->saveCatalogrestrictiongroupRelation($this);
        $this->getCustomerInstance()->saveCatalogrestrictiongroupRelation($this);
        return parent::_afterSave();
    }

    /**
     * get product relation model
     *
     * @access public
     * @return FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup_Product
     * @author Douglas Ianitsky
     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = Mage::getSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_product');
        }
        return $this->_productInstance;
    }

    /**
     * get selected products array
     *
     * @access public
     * @return array
     * @author Douglas Ianitsky
     */
    public function getSelectedProducts()
    {
        if (!$this->hasSelectedProducts()) {
            $products = array();
            foreach ($this->getSelectedProductsCollection() as $product) {
                $products[] = $product;
            }
            $this->setSelectedProducts($products);
        }
        return $this->getData('selected_products');
    }

    /**
     * Retrieve collection selected products
     *
     * @access public
     * @return FVets_CatalogRestrictionGroup_Resource_Catalogrestrictiongroup_Product_Collection
     * @author Douglas Ianitsky
     */
    public function getSelectedProductsCollection()
    {
        $collection = $this->getProductInstance()->getProductCollection($this);
        return $collection;
    }

    /**
     * get customer relation model
     *
     * @access public
     * @return FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup_Customer
     * @author Douglas Ianitsky
     */
    public function getCustomerInstance()
    {
        if (!$this->_customerInstance) {
            $this->_customerInstance = Mage::getSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_customer');
        }
        return $this->_customerInstance;
    }

    /**
     * get selected customers array
     *
     * @access public
     * @return array
     * @author Douglas Ianitsky
     */
    public function getSelectedCustomers()
    {
        if (!$this->hasSelectedCustomers()) {
            $customers = array();
            foreach ($this->getSelectedCustomersCollection() as $customer) {
                $customers[] = $customer;
            }
            $this->setSelectedCustomers($customers);
        }
        return $this->getData('selected_customers');
    }

    /**
     * Retrieve collection selected customers
     *
     * @access public
     * @return FVets_CatalogRestrictionGroup_Resource_Catalogrestrictiongroup_Customer_Collection
     * @author Douglas Ianitsky
     */
    public function getSelectedCustomersCollection()
    {
        $collection = $this->getCustomerInstance()->getCustomerCollection($this);
        return $collection;
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Douglas Ianitsky
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
