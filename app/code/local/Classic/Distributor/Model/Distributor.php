<?php
/**
 * Classic_Distributor extension
 * 
 * NOTICE OF LICENSE
 *
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Distributor model
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Model_Distributor extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'classic_distributor';
    const CACHE_TAG = 'classic_distributor';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'classic_distributor';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'distributor';
    protected $_regionInstance = null;

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
        $this->_init('classic_distributor/distributor');
    }

    /**
     * before save distributor
     *
     * @access protected
     * @return Classic_Distributor_Model_Distributor
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
     * save distributor relation
     *
     * @access public
     * @return Classic_Distributor_Model_Distributor
     * @author Douglas Borella Ianitsky
     */
    protected function _afterSave()
    {
        $this->getRegionInstance()->saveDistributorRelation($this);
        return parent::_afterSave();
    }

    /**
     * get region relation model
     *
     * @access public
     * @return Classic_Distributor_Model_Distributor_Region
     * @author Douglas Borella Ianitsky
     */
    public function getRegionInstance()
    {
        if (!$this->_regionInstance) {
            $this->_regionInstance = Mage::getSingleton('classic_distributor/region');
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
     * @return Classic_Distributor_Resource_Distributor_Region_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedRegionsCollection()
    {
        $collection = $this->getRegionInstance()->getRegionCollection($this);
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
    
    /**
      * get Brands
      *
      * @access public
      * @return array
      * @author Douglas Borella Ianitsky
      */
    public function getBrands()
    {
        if (!$this->getData('brands')) {
            return explode(',', $this->getData('brands'));
        }
        return $this->getData('brands');
    }

	private $_websiteUrl = null;

	public function getWebsiteUrl()
	{
		if ($this->_websiteUrl === null)
		{
			if ($this->getId())
			{
				if ($this->getWebsite() > 0)
				{
					$store_id = Mage::getModel('core/website')->load($this->getWebsite())
						->getDefaultGroup()
						->getDefaultStoreId();

					if (Mage::getStoreConfigFlag('web/url/original_link', $store_id))
					{
						$this->_websiteUrl = Mage::getStoreConfig('web/url/original_link', $store_id);
					}
					else
					{
						$this->_websiteUrl = false;
					}
				}
				else
				{
					$this->_websiteUrl = false;
				}
			}
		}

		return $this->_websiteUrl;
	}
}
