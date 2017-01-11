<?php
/**
 * FVets_Salesrep extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Sales Rep tab on category edit form
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Block_Adminhtml_Catalog_Category_Tab_Salesrep extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Douglas Borella Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_salesrep');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_salesreps'=>1));
        }
    }

    /**
     * get current category
     *
     * @access public
     * @return Mage_Catalog_Model_Category|null
     * @author Douglas Borella Ianitsky
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * prepare the collection
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Catalog_Category_Tab_Salesrep
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('fvets_salesrep/salesrep_collection');
        if ($this->getCategory()->getId()) {
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('fvets_salesrep/category')),
            'related.salesrep_id=main_table.id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare the columns
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Catalog_Category_Tab_Salesrep
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_salesreps',
            array(
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_salesreps',
                'values' => $this->_getSelectedSalesreps(),
                'align'  => 'center',
                'index'  => 'entity_id'
            )
        );
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('fvets_salesrep')->__('Id'),
                'type'   => 'number',
                'align'  => 'left',
                'index'  => 'entity_id',
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('fvets_salesrep')->__('Name'),
                'align'  => 'left',
                'index'  => 'name',
                'renderer' => 'fvets_salesrep/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/salesrep_salesrep/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('fvets_salesrep')->__('Position'),
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
     * Retrieve selected salesreps
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    protected function _getSelectedSalesreps()
    {
        $salesreps = $this->getCategorySalesreps();
        if (!is_array($salesreps)) {
            $salesreps = array_keys($this->getSelectedSalesreps());
        }
        return $salesreps;
    }

    /**
     * Retrieve selected salesreps
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedSalesreps()
    {
        $salesreps = array();
        //used helper here in order not to override the category model
        $selected = Mage::helper('fvets_salesrep/category')->getSelectedSalesreps(Mage::registry('current_category'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $salesrep) {
            $salesreps[$salesrep->getId()] = array('position' => $salesrep->getPosition());
        }
        return $salesreps;
    }

    /**
     * get row url
     *
     * @access public
     * @param FVets_Salesrep_Model_Salesrep
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
            'adminhtml/salesrep_salesrep_catalog_category/salesrepsgrid',
            array(
                'id'=>$this->getCategory()->getId()
            )
        );
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return FVets_Salesrep_Block_Adminhtml_Catalog_Category_Tab_Salesrep
     * @author Douglas Borella Ianitsky
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_salesreps') {
            $salesrepIds = $this->_getSelectedSalesreps();
            if (empty($salesrepIds)) {
                $salesrepIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', array('in'=>$salesrepIds));
            } else {
                if ($salesrepIds) {
                    $this->getCollection()->addFieldToFilter('id', array('nin'=>$salesrepIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
