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
 * Distributor collection resource model
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Model_Resource_Distributor_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('classic_distributor/distributor');
    }

    /**
     * get distributors as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     * @author Douglas Borella Ianitsky
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
     * @author Douglas Borella Ianitsky
     */
    protected function _toOptionHash($valueField='entity_id', $labelField='name')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * add the region filter to collection
     *
     * @access public
     * @param mixed (Mage_Catalog_Model_Region|int) $region
     * @return Classic_Distributor_Model_Resource_Distributor_Collection
     * @author Douglas Borella Ianitsky
     */
    public function addRegionFilter($region)
    {
        if ($region instanceof Mage_Catalog_Model_Region) {
            $region = $region->getId();
        }
        if (!isset($this->_joinedFields['region'])) {
            $this->getSelect()->join(
                array('related_region' => $this->getTable('classic_distributor/region')),
                'related_region.distributor_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_region.region_id = ?', $region);
            $this->_joinedFields['region'] = true;
        }
        return $this;
    }

	/**
	 * add the region filter to collection
	 *
	 * @access public
	 * @param mixed (string|int) $brand
	 * @return Classic_Distributor_Model_Resource_Distributor_Collection
	 * @author Douglas Borella Ianitsky
	 */
	public function addBrandFilter($brand)
	{
		$this->getSelect()->where(
			'FIND_IN_SET ("'.$brand.'", main_table.brands)'
		);

		return $this;
	}

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select
     * @author Douglas Borella Ianitsky
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
