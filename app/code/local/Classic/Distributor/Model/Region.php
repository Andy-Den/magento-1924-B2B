<?php
/**
 * Classic_Distributor extension
 * 
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Distributor region model
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Model_Region extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Douglas Borella Ianitsky
     */
    protected function _construct()
    {
        $this->_init('classic_distributor/region');
    }

    /**
     * Save data for distributor-region relation
     * @access public
     * @param  Classic_Distributor_Model_Distributor $distributor
     * @return Classic_Distributor_Model_Distributor_Region
     * @author Douglas Borella Ianitsky
     */
    public function saveDistributorRelation($distributor)
    {
        $data = $distributor->getRegionsData();
        if (!is_null($data)) {
            $this->_getResource()->saveDistributorRelation($distributor, $data);
        }
        return $this;
    }

    /**
     * get regions for distributor
     *
     * @access public
     * @param Classic_Distributor_Model_Distributor $distributor
     * @return Classic_Distributor_Model_Resource_Distributor_Region_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getRegionCollection($distributor)
    {
        $collection = Mage::getResourceModel('classic_distributor/region_collection')
            ->addDistributorFilter($distributor);
        return $collection;
    }
}
