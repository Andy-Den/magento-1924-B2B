<?php
/**
 * FVets_TablePrice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Table Price tab on category edit form
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Block_Adminhtml_Catalog_Category_Tab_Tableprice extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Douglas Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_tableprice');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_tablesprices'=>1));
        }
    }

    /**
     * get current category
     *
     * @access public
     * @return Mage_Catalog_Model_Category|null
     * @author Douglas Ianitsky
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * prepare the collection
     *
     * @access protected
     * @return FVets_TablePrice_Block_Adminhtml_Catalog_Category_Tab_Tableprice
     * @author Douglas Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('fvets_tableprice/tableprice_collection');
        if ($this->getCategory()->getId()) {
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('fvets_tableprice/category')),
            'related.tableprice_id=main_table.entity_id AND '.$constraint,
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
     * @return FVets_TablePrice_Block_Adminhtml_Catalog_Category_Tab_Tableprice
     * @author Douglas Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_tablesprices',
            array(
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_tablesprices',
                'values' => $this->_getSelectedTablesprices(),
                'align'  => 'center',
                'index'  => 'entity_id'
            )
        );
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('fvets_tableprice')->__('Id'),
                'type'   => 'number',
                'align'  => 'left',
                'index'  => 'entity_id',
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('fvets_tableprice')->__('Name'),
                'align'  => 'left',
                'index'  => 'name',
                'renderer' => 'fvets_tableprice/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/tableprice_tableprice/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('fvets_tableprice')->__('Position'),
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
     * Retrieve selected tablesprices
     *
     * @access protected
     * @return array
     * @author Douglas Ianitsky
     */
    protected function _getSelectedTablesprices()
    {
        $tablesprices = $this->getCategoryTablesprices();
        if (!is_array($tablesprices)) {
            $tablesprices = array_keys($this->getSelectedTablesprices());
        }
        return $tablesprices;
    }

    /**
     * Retrieve selected tablesprices
     *
     * @access protected
     * @return array
     * @author Douglas Ianitsky
     */
    public function getSelectedTablesprices()
    {
        $tablesprices = array();
        //used helper here in order not to override the category model
        $selected = Mage::helper('fvets_tableprice/category')->getSelectedTablesprices(Mage::registry('current_category'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $tableprice) {
            $tablesprices[$tableprice->getId()] = array('position' => $tableprice->getPosition());
        }
        return $tablesprices;
    }

    /**
     * get row url
     *
     * @access public
     * @param FVets_TablePrice_Model_Tableprice
     * @return string
     * @author Douglas Ianitsky
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
     * @author Douglas Ianitsky
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'adminhtml/tableprice_tableprice_catalog_category/tablespricesgrid',
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
     * @return FVets_TablePrice_Block_Adminhtml_Catalog_Category_Tab_Tableprice
     * @author Douglas Ianitsky
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_tablesprices') {
            $tablepriceIds = $this->_getSelectedTablesprices();
            if (empty($tablepriceIds)) {
                $tablepriceIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$tablepriceIds));
            } else {
                if ($tablepriceIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$tablepriceIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
