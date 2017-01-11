<?php
/**
 * FVets_Salesrep extension
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 */
/**
 * Salesrep region model
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Model_Region extends Mage_Core_Model_Abstract
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
        $this->_init('fvets_salesrep/region');
    }

    /**
     * Save data for salesrep-region relation
     * @access public
     * @param  FVets_Salesrep_Model_Salesrep $salesrep
     * @return FVets_Salesrep_Model_Salesrep_Region
     * @author Douglas Borella Ianitsky
     */
    public function saveSalesrepRelation($salesrep)
    {
        $data = $salesrep->getRegionsData();
        if (!is_null($data)) {
            $this->_getResource()->saveSalesrepRelation($salesrep, $data);
        }
        return $this;
    }

    /**
     * get regions for salesrep
     *
     * @access public
     * @param FVets_Salesrep_Model_Salesrep $salesrep
     * @return FVets_Salesrep_Model_Resource_Salesrep_Region_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getRegionCollection($salesrep)
    {
        $collection = Mage::getResourceModel('fvets_salesrep/region_collection')
            ->addSalesrepFilter($salesrep);
        return $collection;
    }
}
