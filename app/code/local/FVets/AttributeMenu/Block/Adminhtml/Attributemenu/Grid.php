<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * AttributeMenu admin grid block
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('attributemenuGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('fvets_attributemenu/attributemenu')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('fvets_attributemenu')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('fvets_attributemenu')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('fvets_attributemenu')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('fvets_attributemenu')->__('Enabled'),
                    '0' => Mage::helper('fvets_attributemenu')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'attribute',
            array(
                'header' => Mage::helper('fvets_attributemenu')->__('Attribute'),
                'index'  => 'attribute',
                'type'  => 'options',
                'options' => Mage::helper('fvets_attributemenu')->convertOptions(
                    Mage::getModel('fvets_attributemenu/attributemenu_attribute_source_attribute')->getAllOptions(false)
                )

            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'store_id',
                array(
                    'header'     => Mage::helper('fvets_attributemenu')->__('Store Views'),
                    'index'      => 'store_id',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback'=> array($this, '_filterStoreCondition'),
                )
            );
        }
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('fvets_attributemenu')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('fvets_attributemenu')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('fvets_attributemenu')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('fvets_attributemenu')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('fvets_attributemenu')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('attributemenu');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('fvets_attributemenu')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('fvets_attributemenu')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('fvets_attributemenu')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('fvets_attributemenu')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('fvets_attributemenu')->__('Enabled'),
                            '0' => Mage::helper('fvets_attributemenu')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'attribute',
            array(
                'label'      => Mage::helper('fvets_attributemenu')->__('Change Attribute'),
                'url'        => $this->getUrl('*/*/massAttribute', array('_current'=>true)),
                'additional' => array(
                    'flag_attribute' => array(
                        'name'   => 'flag_attribute',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('fvets_attributemenu')->__('Attribute'),
                        'values' => Mage::getModel('fvets_attributemenu/attributemenu_attribute_source_attribute')
                            ->getAllOptions(true),

                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param FVets_AttributeMenu_Model_Attributemenu
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * filter store column
     *
     * @access protected
     * @param FVets_AttributeMenu_Model_Resource_Attributemenu_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Grid
     * @author Ultimate Module Creator
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addStoreFilter($value);
        return $this;
    }
}
