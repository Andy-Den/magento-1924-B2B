<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group tab on product edit form
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalog_Product_Edit_Tab_Catalogrestrictiongroup extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access public
     * @author Douglas Ianitsky
     */

    public function __construct()
    {
        parent::__construct();
        $this->setId('catalogrestrictiongroup_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProduct()->getId()) {
            $this->setDefaultFilter(array('in_catalogrestrictiongroups'=>1));
        }
    }

    /**
     * prepare the catalogrestrictiongroup collection
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalog_Product_Edit_Tab_Catalogrestrictiongroup
     * @author Douglas Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup_collection');
        if ($this->getProduct()->getId()) {
            $constraint = 'related.product_id='.$this->getProduct()->getId();
        } else {
            $constraint = 'related.product_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('fvets_catalogrestrictiongroup/entity_product')),
            'related.catalogrestrictiongroup_id=main_table.entity_id AND '.$constraint,
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
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalog_Product_Edit_Tab_Catalogrestrictiongroup
     * @author Douglas Ianitsky
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalog_Product_Edit_Tab_Catalogrestrictiongroup
     * @author Douglas Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_catalogrestrictiongroups',
            array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_catalogrestrictiongroups',
                'values'=> $this->_getSelectedCatalogrestrictiongroups(),
                'align' => 'center',
                'index' => 'entity_id'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('fvets_catalogrestrictiongroup')->__('Name'),
                'align'  => 'left',
                'index'  => 'name',
                'renderer' => 'fvets_catalogrestrictiongroup/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/catalogrestrictiongroup_catalogrestrictiongroup/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('fvets_catalogrestrictiongroup')->__('Position'),
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
     * Retrieve selected catalogrestrictiongroups
     *
     * @access protected
     * @return array
     * @author Douglas Ianitsky
     */
    protected function _getSelectedCatalogrestrictiongroups()
    {
        $catalogrestrictiongroups = $this->getProductCatalogrestrictiongroups();
        if (!is_array($catalogrestrictiongroups)) {
            $catalogrestrictiongroups = array_keys($this->getSelectedCatalogrestrictiongroups());
        }
        return $catalogrestrictiongroups;
    }

    /**
     * Retrieve selected catalogrestrictiongroups
     *
     * @access protected
     * @return array
     * @author Douglas Ianitsky
     */
    public function getSelectedCatalogrestrictiongroups()
    {
        $catalogrestrictiongroups = array();
        //used helper here in order not to override the product model
        $selected = Mage::helper('fvets_catalogrestrictiongroup/product')->getSelectedCatalogrestrictiongroups(Mage::registry('current_product'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $catalogrestrictiongroup) {
            $catalogrestrictiongroups[$catalogrestrictiongroup->getId()] = array('position' => $catalogrestrictiongroup->getPosition());
        }
        return $catalogrestrictiongroups;
    }

    /**
     * get row url
     *
     * @access public
     * @param FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup
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
            '*/*/catalogrestrictiongroupsGrid',
            array(
                'id'=>$this->getProduct()->getId()
            )
        );
    }

    /**
     * get the current product
     *
     * @access public
     * @return Mage_Catalog_Model_Product
     * @author Douglas Ianitsky
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalog_Product_Edit_Tab_Catalogrestrictiongroup
     * @author Douglas Ianitsky
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_catalogrestrictiongroups') {
            $catalogrestrictiongroupIds = $this->_getSelectedCatalogrestrictiongroups();
            if (empty($catalogrestrictiongroupIds)) {
                $catalogrestrictiongroupIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$catalogrestrictiongroupIds));
            } else {
                if ($catalogrestrictiongroupIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$catalogrestrictiongroupIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
