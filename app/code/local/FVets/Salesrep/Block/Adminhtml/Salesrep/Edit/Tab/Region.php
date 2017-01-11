<?php
/**
 * FVets_Salesrep extension
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 */
/**
 * Salesrep - region relation edit block
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Block_Adminhtml_Salesrep_Edit_Tab_Region extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access protected
     * @author Douglas Borella Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('region_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
		$this->setDefaultLimit(30);
        $this->setUseAjax(true);
        if ($this->getSalesrep()->getId()) {
            $this->setDefaultFilter(array('region_id'=>1));
        }
    }

    /**
     * prepare the region collection
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Edit_Tab_Region
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareCollection()
    {

		$collection = Mage::getResourceModel('directory/region_collection');
		$collection->addFieldToSelect('default_name');
		$collection->addFieldToSelect('region_id');
		$collection->addFieldToSelect('code');
		$collection->addFieldToFilter('country_id', 'BR');

        $this->setCollection($collection);

        parent::_prepareCollection();

		//echo $this->getCollection()->getSelect()->__toString();

        return $this;
    }

    /**
     * prepare mass action grid
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Edit_Tab_Region
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
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Edit_Tab_Region
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'region_id',
            array(
                'header_css_class'  => 'a-center',
                'type'  			=> 'checkbox',
                'name'  			=> 'region_id',
                'values'			=> $this->_getSelectedRegions(),
                'align' 			=> 'center',
                'index'				=> 'region_id'
            )
        );

		$this->addColumn(
			'default_name',
			array(
				'header' => $this->__('Name'),
				'align'  => 'left',
				'index'  => 'default_name',
			)
		);

        $this->addColumn(
            'code',
            array(
                'header' => $this->__('Region Code'),
                'align'  => 'left',
                'index'  => 'code',
            )
        );

        $this->addColumn(
            'country_id',
            array(
                'header'        => $this->__('Country'),
                'width'         => '1',
                'index'         => 'country_id'
            )
        );

        $this->addColumn(
            'position',
            array(
                'header'         => $this->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
    }

    /**
     * Retrieve selected regions
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    protected function _getSelectedRegions()
    {
        $regions = $this->getSalesrepRegions();
        if (!is_array($regions)) {
            $regions = array_keys($this->getSelectedRegions());
        }
        return $regions;
    }

    /**
     * Retrieve selected regions
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedRegions()
    {
        $regions = array();
        $selected = Mage::registry('fvets_salesrep')->getSelectedRegions();
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $region) {
            $regions[$region->getId()] = array('position' => $region->getPosition());
        }
        return $regions;
    }

    /**
     * get row url
     *
     * @access public
     * @param FVets_Salesrep_Model_Region
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
            '*/*/regionsGrid',
            array(
                'id' => $this->getSalesrep()->getId()
            )
        );
    }

    /**
     * get the current salesrep
     *
     * @access public
     * @return FVets_Salesrep_Model_Salesrep
     * @author Douglas Borella Ianitsky
     */
    public function getSalesrep()
    {
        return Mage::registry('fvets_salesrep');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Edit_Tab_Region
     * @author Douglas Borella Ianitsky
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in region flag
        if ($column->getId() == 'region_id') {
            $regionIds = $this->_getSelectedRegions();
            if (empty($regionIds)) {
                $regionIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.region_id', array('in' => $regionIds));
            } else {
                if ($regionIds) {
                    $this->getCollection()->addFieldToFilter('main_table.region_id', array('nin' => $regionIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
