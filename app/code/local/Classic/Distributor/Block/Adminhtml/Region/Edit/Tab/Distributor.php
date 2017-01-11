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
 * Distributor tab on region edit form
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Block_Adminhtml_Region_Edit_Tab_Distributor extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access public
     * @author Douglas Borella Ianitsky
     */

    public function __construct()
    {
        parent::__construct();
        $this->setId('distributor_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getRegion()->getId()) {
            $this->setDefaultFilter(array('in_distributors'=>1));
        }
    }

    /**
     * prepare the distributor collection
     *
     * @access protected
     * @return Classic_Distributor_Block_Adminhtml_Catalog_Region_Edit_Tab_Distributor
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('classic_distributor/collection');
        if ($this->getRegion()->getId()) {
            $constraint = 'related.region_id='.$this->getRegion()->getId();
        } else {
            $constraint = 'related.region_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('classic_distributor/region')),
            'related.distributor_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * prepare mass action grid
     *
     * @access protected
     * @return Classic_Distributor_Block_Adminhtml_Catalog_Region_Edit_Tab_Distributor
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return Classic_Distributor_Block_Adminhtml_Catalog_Region_Edit_Tab_Distributor
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_distributors',
            array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_distributors',
                'values'=> $this->_getSelectedDistributors(),
                'align' => 'center',
                'index' => 'entity_id'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('classic_distributor')->__('Name'),
                'align'  => 'left',
                'index'  => 'name',
                'renderer' => 'classic_distributor/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/distributor_distributor/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('classic_distributor')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve selected distributors
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    protected function _getSelectedDistributors()
    {
        $distributors = $this->getRegionDistributors();
        if (!is_array($distributors)) {
            $distributors = array_keys($this->getSelectedDistributors());
        }
        return $distributors;
    }

    /**
     * Retrieve selected distributors
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedDistributors()
    {
        $distributors = array();
        //used helper here in order not to override the region model
        $selected = Mage::helper('classic_distributor/region')->getSelectedDistributors(Mage::registry('current_region'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $distributor) {
            $distributors[$distributor->getId()] = array('position' => $distributor->getPosition());
        }
        return $distributors;
    }

    /**
     * get row url
     *
     * @access public
     * @param Classic_Distributor_Model_Distributor
     * @return string
     * @author Douglas Borella Ianitsky
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @access public
     * @return string
     * @author Douglas Borella Ianitsky
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/distributorsGrid',
            array(
                'id'=>$this->getRegion()->getId()
            )
        );
    }

    /**
     * get the current region
     *
     * @access public
     * @return Mage_Catalog_Model_Region
     * @author Douglas Borella Ianitsky
     */
    public function getRegion()
    {
        return Mage::registry('current_region');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Classic_Distributor_Block_Adminhtml_Catalog_Region_Edit_Tab_Distributor
     * @author Douglas Borella Ianitsky
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_distributors') {
            $distributorIds = $this->_getSelectedDistributors();
            if (empty($distributorIds)) {
                $distributorIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$distributorIds));
            } else {
                if ($distributorIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$distributorIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
