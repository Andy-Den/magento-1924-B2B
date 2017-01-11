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
 * Distributor - region relation resource model collection
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Model_Resource_Region_Collection extends Mage_Directory_Model_Resource_Region_Collection
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
     * @return Classic_Distributor_Model_Resource_Distributor_Region_Collection
     * @author Douglas Borella Ianitsky
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('classic_distributor/region')),
                'related.region_id = main_table.region_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add distributor filter
     *
     * @access public
     * @param Classic_Distributor_Model_Distributor | int $distributor
     * @return Classic_Distributor_Model_Resource_Distributor_Region_Collection
     * @author Douglas Borella Ianitsky
     */
    public function addDistributorFilter($distributor)
    {
        if ($distributor instanceof Classic_Distributor_Model_Distributor) {
            $distributor = $distributor->getId();
        }
        if (!$this->_joinedFields ) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.distributor_id = ?', $distributor);
        return $this;
    }
}
