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
 * Condition model
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Model_Condition extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'fvets_payment_condition';
    const CACHE_TAG = 'fvets_payment_condition';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'fvets_payment_condition';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'condition';
    protected $_customerInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('fvets_payment/condition');
    }

    /**
     * before save condition
     *
     * @access protected
     * @return FVets_Payment_Model_Condition
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
     * save condition relation
     *
     * @access public
     * @return FVets_Payment_Model_Condition
     */
    protected function _afterSave()
    {
        $this->getCustomerInstance()->saveConditionRelation($this);
		$this->getCategoryInstance()->saveConditionRelation($this);
		$this->getExcludedCategoryInstance()->saveConditionRelation($this);
        return parent::_afterSave();
    }

    /**
     * get customer relation model
     *
     * @access public
     * @return FVets_Payment_Model_Condition_Customer
     */
    public function getCustomerInstance()
    {
        if (!$this->_customerInstance) {
            $this->_customerInstance = Mage::getSingleton('fvets_payment/condition_customer');
        }
        return $this->_customerInstance;
    }

    /**
     * get selected customers array
     *
     * @access public
     * @return array
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
     * @return FVets_Payment_Resource_Condition_Customer_Collection
     */
    public function getSelectedCustomersCollection()
    {
        $collection = $this->getCustomerInstance()->getCustomerCollection($this);
        return $collection;
    }

	/**
	 * get category relation model
	 *
	 * @access public
	 * @return FVets_Payment_Model_Condition_Category
	 * @author Ultimate Module Creator
	 */
	public function getCategoryInstance()
	{
		if (!$this->_categoryInstance) {
			$this->_categoryInstance = Mage::getSingleton('fvets_payment/condition_category');
		}
		return $this->_categoryInstance;
	}

	/**
	 * get selected categories array
	 *
	 * @access public
	 * @return array
	 * @author Ultimate Module Creator
	 */
	public function getSelectedCategories()
	{
		if (!$this->hasSelectedCategories()) {
			$categories = array();
			foreach ($this->getSelectedCategoriesCollection() as $category) {
				$categories[] = $category;
			}
			$this->setSelectedCategories($categories);
		}
		return $this->getData('selected_categories');
	}

	/**
	 * Retrieve collection selected categories
	 *
	 * @access public
	 * @return FVets_Payment_Resource_Condition_Category_Collection
	 * @author Ultimate Module Creator
	 */
	public function getSelectedCategoriesCollection()
	{
		$collection = $this->getCategoryInstance()->getCategoryCollection($this);
		return $collection;
	}

	/**
	 * get category relation model
	 *
	 * @access public
	 * @return FVets_Payment_Model_Resource_Condition_Exclude
	 * @author Ultimate Module Creator
	 */
	public function getExcludedCategoryInstance()
	{
		if (!$this->_excludedCategoryInstance) {
			$this->_excludedCategoryInstance = Mage::getSingleton('fvets_payment/condition_excluded');
		}
		return $this->_excludedCategoryInstance;
	}

	/**
	 * get excluded categories array
	 *
	 * @access public
	 * @return array
	 * @author Ultimate Module Creator
	 */
	public function getExcludedCategories()
	{
		if (!$this->hasExcludedCategories()) {
			$categories = array();
			foreach ($this->getExcludedCategoriesCollection() as $category) {
				$categories[] = $category;
			}
			$this->setExcludedCategories($categories);
		}
		return $this->getData('excluded_categories');
	}

	/**
	 * Retrieve collection Excluded categories
	 *
	 * @access public
	 * @return FVets_Payment_Model_Resource_Condition_Exclude_Collection
	 * @author Ultimate Module Creator
	 */
	public function getExcludedCategoriesCollection()
	{
		$collection = $this->getExcludedCategoryInstance()->getExcludedCategoryCollection($this);
		return $collection;
	}

    /**
     * get default values
     *
     * @access public
     * @return array
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        $values['split'] = '1';

        return $values;
    }
    
    /**
      * get Payment Methods
      *
      * @access public
      * @return array
      */
    public function getPaymentMethods()
    {
        if (!$this->getData('payment_methods')) {
            return explode(',', $this->getData('payment_methods'));
        }
        return $this->getData('payment_methods');
    }
    /**
      * get Apply to Groups
      *
      * @access public
      * @return array
      */
    public function getApplyToGroups()
    {
        if (!$this->getData('apply_to_groups')) {
            return explode(',', $this->getData('apply_to_groups'));
        }
        return $this->getData('apply_to_groups');
    }

	public function getData($key='', $index=null)
	{
		if ($key == 'discount')
		{
			if (Mage::getSingleton('checkout/session')->getQuote()->getStopConditionDiscount())
			{
				return 0;
			}
		}

		return parent::getData($key, $index);
	}
}
