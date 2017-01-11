<?php

class FVets_Salesrep_Model_Salesrep extends Mage_Core_Model_Abstract
{

	private $_brands = null;

    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'fvets_salesrep';
    const CACHE_TAG = 'fvets_salesrep';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'fvets_salesrep';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'salesrep';
    protected $_categoryInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('fvets_salesrep/salesrep');
    }

    /**
     * before save sales rep
     *
     * @access protected
     * @return FVets_Salesrep_Model_Salesrep
     * @author Douglas Borella Ianitsky
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
     * save sales rep relation
     *
     * @access public
     * @return FVets_Salesrep_Model_Salesrep
     * @author Douglas Borella Ianitsky
     */
    protected function _afterSave()
    {
        $this->getCategoryInstance()->saveSalesrepRelation($this);
		$this->getRegionInstance()->saveSalesrepRelation($this);
        return parent::_afterSave();
    }

    /**
     * get category relation model
     *
     * @access public
     * @return FVets_Salesrep_Model_Salesrep_Category
     * @author Douglas Borella Ianitsky
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('fvets_salesrep/category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected categories array
     *
     * @access public
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedCategories($filter_active = true)
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection($filter_active) as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

	/**
	 * get region relation model
	 *
	 * @access public
	 * @return FVets_Salesrep_Model_Salesrep_Region
	 * @author Douglas Borella Ianitsky
	 */
	public function getRegionInstance()
	{
		if (!$this->_regionInstance) {
			$this->_regionInstance = Mage::getSingleton('fvets_salesrep/region');
		}
		return $this->_regionInstance;
	}

	/**
	 * get selected regions array
	 *
	 * @access public
	 * @return array
	 * @author Douglas Borella Ianitsky
	 */
	public function getSelectedRegions()
	{
		if (!$this->hasSelectedRegions()) {
			$regions = array();
			foreach ($this->getSelectedRegionsCollection() as $region) {
				$regions[] = $region;
			}
			$this->setSelectedRegions($regions);
		}
		return $this->getData('selected_regions');
	}

	/**
	 * Retrieve collection selected regions
	 *
	 * @access public
	 * @return FVets_Salesrep_Resource_Salesrep_Region_Collection
	 * @author Douglas Borella Ianitsky
	 */
	public function getSelectedRegionsCollection()
	{
		$collection = $this->getRegionInstance()->getRegionCollection($this);
		return $collection;
	}

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return FVets_Salesrep_Resource_Salesrep_Category_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedCategoriesCollection($filter_active = true, $storeId = null)
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this, $filter_active);

		if (isset($storeId)) {
			$store = Mage::getModel('core/store')->load($storeId);
			$rootCategoryId = $store->getRootCategoryId();
			$rootpath = Mage::getModel('catalog/category')
				->setStoreId($storeId)
				->load($rootCategoryId)
				->getPath();
			$collection->addAttributeToFilter('path', array("like" => $rootpath . "/" . "%"));
		}

        return $collection;
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
		$collection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('fvets_salesrep',array("finset"=>array($this->getId())));
		;
		return $collection;
	}

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }

	public function sendRepComissionEmail($order, $paymentBlockHtml, $rep, $storeId = '0')
	{
		if (!Mage::helper('fvets_core')->canSentEmailFromCustomer($order->getCustomerEmail(), $rep->getEmail()))
		{
			return $this;
		}

		$translate = Mage::getSingleton('core/translate');

		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);

		Mage::getModel('core/email_template')
			->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
			->sendTransactional(
				Mage::getStoreConfig('fvets_salesrep/general/email_template', $storeId),
				Mage::getStoreConfig('fvets_salesrep/general/identity', $storeId),
				$rep->getEmail(),
				$rep->getName(),
				array('order' => $order, 'billing' => $order->getBillingAddress(), 'payment_html' => $paymentBlockHtml, 'rep' => $rep));

		$translate->setTranslateInline(true);

		return $this;
	}

	// captura todos os vínculos dos representantes do usuário que esta logado
	public function getCustomerReps()
	{
		$collection = Mage::getResourceModel('fvets_salesrep/salesrep_collection')
			->addFieldToFilter('id', array('in' => explode(',',Mage::getSingleton('customer/session')->getCustomer()->getFvetsSalesrep())))
			->addStoresToFilter(Mage::app()->getStore()->getId());
		return $collection;
	}
    
}
