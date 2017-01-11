<?php
/**
 * FVets_Salesrep extension
 * 
 * NOTICE OF LICENSE
 *
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 */
/**
 * Salesrep - region relation resource model collection
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Model_Resource_Region_Collection extends Mage_Directory_Model_Resource_Region_Collection
{
    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return FVets_Salesrep_Model_Resource_Salesrep_Region_Collection
     * @author Douglas Borella Ianitsky
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('fvets_salesrep/region')),
                'related.region_id = main_table.region_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add salesrep filter
     *
     * @access public
     * @param FVets_Salesrep_Model_Salesrep | int $salesrep
     * @return FVets_Salesrep_Model_Resource_Salesrep_Region_Collection
     * @author Douglas Borella Ianitsky
     */
    public function addSalesrepFilter($salesrep)
    {
        if ($salesrep instanceof FVets_Salesrep_Model_Salesrep) {
            $salesrep = $salesrep->getId();
        }
        if (!$this->_joinedFields ) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.salesrep_id = ?', $salesrep);
        return $this;
    }
}
