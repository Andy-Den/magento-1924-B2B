<?php
/**
 * FVets_Salesrep extension
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 */
/**
 * Salesrep - region relation model
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Model_Resource_Region extends Mage_Core_Model_Resource_Db_Abstract
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
        $this->_init('fvets_salesrep/region', 'rel_id');
    }
    /**
     * Save salesrep - region relations
     *
     * @access public
     * @param FVets_Salesrep_Model_Salesrep $salesrep
     * @param array $data
     * @return FVets_Salesrep_Model_Resource_Salesrep_Region
     * @author Douglas Borella Ianitsky
     */
    public function saveSalesrepRelation($salesrep, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('salesrep_id=?', $salesrep->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $regionId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'salesrep_id' => $salesrep->getId(),
                    'region_id'    => $regionId,
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Save  region - salesrep relations
     *
     * @access public
     * @param Mage_Catalog_Model_Region $prooduct
     * @param array $data
     * @return FVets_Salesrep_Model_Resource_Salesrep_Region
     * @@author Douglas Borella Ianitsky
     */
    public function saveRegionRelation($region, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('region_id=?', $region->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $salesrepId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'salesrep_id' => $salesrepId,
                    'region_id'    => $region->getId(),
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }
}
