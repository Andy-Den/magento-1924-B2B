<?php
/**
 * Classic_Distributor extension
 * 
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Distributor - region relation model
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Model_Resource_Region extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Douglas Borella Ianitsky
     */
    protected function  _construct()
    {
        $this->_init('classic_distributor/region', 'rel_id');
    }
    /**
     * Save distributor - region relations
     *
     * @access public
     * @param Classic_Distributor_Model_Distributor $distributor
     * @param array $data
     * @return Classic_Distributor_Model_Resource_Distributor_Region
     * @author Douglas Borella Ianitsky
     */
    public function saveDistributorRelation($distributor, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('distributor_id=?', $distributor->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $regionId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'distributor_id' => $distributor->getId(),
                    'region_id'    => $regionId,
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Save  region - distributor relations
     *
     * @access public
     * @param Mage_Catalog_Model_Region $prooduct
     * @param array $data
     * @return Classic_Distributor_Model_Resource_Distributor_Region
     * @@author Douglas Borella Ianitsky
     */
    public function saveRegionRelation($region, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('region_id=?', $region->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $distributorId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'distributor_id' => $distributorId,
                    'region_id'    => $region->getId(),
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }
}
