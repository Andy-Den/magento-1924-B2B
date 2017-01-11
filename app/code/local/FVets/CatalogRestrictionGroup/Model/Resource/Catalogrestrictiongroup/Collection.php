<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group collection resource model
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('fvets_catalogrestrictiongroup/catalogrestrictiongroup');
    }

    /**
     * get restriction groups as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     * @author Douglas Ianitsky
     */
    protected function _toOptionArray($valueField='entity_id', $labelField='name', $additional=array())
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * get options hash
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @return array
     * @author Douglas Ianitsky
     */
    protected function _toOptionHash($valueField='entity_id', $labelField='name')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * add the product filter to collection
     *
     * @access public
     * @param mixed (Mage_Catalog_Model_Product|int) $product
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Collection
     * @author Douglas Ianitsky
     */
    public function addProductFilter($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $product = $product->getId();
        }
        if (!isset($this->_joinedFields['product'])) {
            $this->getSelect()->join(
                array('related_product' => $this->getTable('fvets_catalogrestrictiongroup/entity_product')),
                'related_product.catalogrestrictiongroup_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_product.product_id = ?', $product);
            $this->_joinedFields['product'] = true;
        }
        return $this;
    }

    /**
     * add the customer filter to collection
     *
     * @access public
     * @param mixed (Mage_Catalog_Model_Customer|int) $customer
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Collection
     * @author Douglas Ianitsky
     */
    public function addCustomerFilter($customer)
    {
        if (!$customer instanceof Mage_Customer_Model_Customer) {
					$customer = Mage::getCollection('customer')->load($customer);
				}
        if (!isset($this->_joinedFields['customer'])) {
            $this->getSelect()->join(
                array('related_customer' => $this->getTable('fvets_catalogrestrictiongroup/entity_customer')),
                'related_customer.catalogrestrictiongroup_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_customer.customer_id = ?', $customer->getId());
            $this->_joinedFields['customer'] = true;
        }
        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select
     * @author Douglas Ianitsky
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
