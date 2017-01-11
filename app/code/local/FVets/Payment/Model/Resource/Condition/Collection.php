<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Condition collection resource model
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Model_Resource_Condition_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    protected $_split = null;

    protected $_paymentMethods = array();

    /**
     * constructor
     *
     * @access public
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('fvets_payment/condition');
        $this->_map['fields']['store'] = 'store_table.store_id';
		$this->_map['fields']['category'] = 'category_table.category_id';
    }

    /**
     * Add filter by store
     *
     * @access public
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return FVets_Payment_Model_Resource_Condition_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!isset($this->_joinedFields['store'])) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }
            if (!is_array($store)) {
                $store = array($store);
            }
            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }
            $this->addFilter('store', array('in' => $store), 'public');
            $this->_joinedFields['store'] = true;
        }
        return $this;
    }

    /**
     * Add filter by website
     *
     * @access public
     * @param int|Mage_Core_Model_Website $website
     * @param bool $withAdmin
     * @return FVets_Payment_Model_Resource_Condition_Collection
     */
    public function addWebsiteFilter($website, $withAdmin = true)
    {
        if ($website instanceof Mage_Core_Model_Website) {

        } else {
            $website = Mage::app()->getWebsite($website);
        }

        $stores = array();
        foreach ($website->getGroups() as $group)
        {
            foreach ($group->getStores() as $store)
            {
                $stores[] = $store->getId();
            }
        }

        return $this->addStoreFilter($stores, $withAdmin);
    }

    /**
     * Join store relation table if there is store filter
     *
     * @access protected
     * @return FVets_Payment_Model_Resource_Condition_Collection
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('fvets_payment/condition_store')),
                'main_table.entity_id = store_table.condition_id',
                array()
            )
            ->group('main_table.entity_id');
            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }

        return parent::_renderFiltersBefore();
    }

    /**
     * get conditions as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
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
     */
    protected function _toOptionHash($valueField='entity_id', $labelField='name')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * add the customer filter to collection
     *
     * @access public
     * @param mixed (Mage_Customer_Model_Customer|int) $customer
     * @return FVets_Payment_Model_Resource_Condition_Collection
     */
    public function addCustomerFilter($customer_id)
    {
        if ($customer_id instanceof Mage_Customer_Model_Customer) {
            $customer = $customer_id;
            $customer_id = $customer->getId();
        } else {
            $customer = Mage::getCollection('customer')->load($customer_id);
        }
        if (!isset($this->_joinedFields['customer'])) {
            $this->getSelect()->joinLeft(
                array('related_customer' => $this->getTable('fvets_payment/condition_customer')),
                'related_customer.condition_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()
                ->where('(related_customer.customer_id = ?', $customer_id)
                ->orWhere('main_table.apply_to_all = ?', 1)
                ->orWhere('FIND_IN_SET(?, apply_to_groups))', $customer->getGroupId())
            ;

            $this->_joinedFields['customer'] = true;
        }
        return $this;
    }

    /**
     * add the customer filter to collection
     *
     * @access public
     * @param mixed (Mage_Customer_Model_Customer|int) $customer
     * @return FVets_Payment_Model_Resource_Condition_Collection
     */
    public function addAdminCustomerFilter($customer_id)
    {
        if ($customer_id instanceof Mage_Customer_Model_Customer) {
            $customer_id = $customer_id->getId();
        }
        if (!isset($this->_joinedFields['customer'])) {
            $this->getSelect()->joinLeft(
                array('related_customer' => $this->getTable('fvets_payment/condition_customer')),
                'related_customer.condition_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()
                ->where('related_customer.customer_id = ?', $customer_id)
            ;

            $this->_joinedFields['customer'] = true;
        }
        return $this;
    }

	/**
	 * add the category filter to collection
	 *
	 * @access public
	 * @param mixed (Mage_Catalog_Model_Category|int) $category
	 * @return FVets_Payment_Model_Resource_Condition_Collection
	 * @author Ultimate Module Creator
	 */
	public function addCategoryFilter($category)
	{
		if ($category instanceof Mage_Catalog_Model_Category) {
			$category = $category->getId();
		}
		if (!isset($this->_joinedFields['category'])) {
			$this->getSelect()->join(
				array('related_category' => $this->getTable('fvets_payment/condition_category')),
				'related_category.condition_id = main_table.entity_id',
				array('position')
			);
			$this->getSelect()->where('related_category.category_id = ?', $category);
			$this->_joinedFields['category'] = true;
		}
		return $this;
	}

	/**
	 * add the categories filter to collection
	 *
	 * @access public
	 * @param mixed (Mage_Catalog_Model_Category|array) $categories
	 * @return FVets_Payment_Model_Resource_Condition_Collection
	 * @author Ultimate Module Creator
	 */
	public function addCategoriesFilter($categories, $null = false)
	{
		if ($categories instanceof Varien_Data_Collection_Db) {
			$categories = $categories->getIds();
		}
		if (!isset($this->_joinedFields['category'])) {
			$this->getSelect()->joinLeft(
				array('related_category' => $this->getTable('fvets_payment/condition_category')),
				'related_category.condition_id = main_table.entity_id',
				array('position')
			);
			if ($null)
				$this->addFieldToFilter('related_category.category_id', array($categories, array('null' => true)));
			else
				$this->addFieldToFilter('related_category.category_id', array($categories));

			//$this->getSelect()->group(array('main_table.id_erp'));

			$this->_joinedFields['category'] = true;
		}
		return $this;
	}

	/**
	 * exclude the categories filter to collection
	 *
	 * @access public
	 * @param mixed (Mage_Catalog_Model_Category|array) $categories
	 * @return FVets_Payment_Model_Resource_Condition_Collection
	 * @author Ultimate Module Creator
	 */
	public function excludeCategoriesFilter($categories)
	{
		if ($categories instanceof Varien_Data_Collection_Db) {
			$categories = $categories->getIds();
		}
		if (!isset($this->_joinedFields['excluded'])) {
			$this->getSelect()->joinLeft(
				array('excluded_category' => $this->getTable('fvets_payment/condition_excluded')),
				'excluded_category.condition_id = main_table.entity_id',
				array('position')
			);

			for ($i = 0; $i < count($categories); $i++)
			{
				$categories[$i] = "excluded_category.category_id != '" . $categories[$i] . "'";
			}

			$where = '(' . implode(' AND ', $categories) . ') OR excluded_category.category_id IS NULL';

			$this->getSelect()->where($where);

			$this->_joinedFields['excluded'] = true;
		}
		return $this;
	}

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
