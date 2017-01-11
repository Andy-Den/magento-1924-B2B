<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group admin grid block
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('catalogrestrictiongroupGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Grid
     * @author Douglas Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Grid
     * @author Douglas Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('fvets_catalogrestrictiongroup')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('fvets_catalogrestrictiongroup')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('fvets_catalogrestrictiongroup')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('fvets_catalogrestrictiongroup')->__('Enabled'),
                    '0' => Mage::helper('fvets_catalogrestrictiongroup')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'website_id',
            array(
                'header' => Mage::helper('fvets_catalogrestrictiongroup')->__('Website'),
                'index'  => 'website_id',
                'type'=> 'website',

            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('fvets_catalogrestrictiongroup')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('fvets_catalogrestrictiongroup')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('fvets_catalogrestrictiongroup')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('fvets_catalogrestrictiongroup')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('fvets_catalogrestrictiongroup')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Grid
     * @author Douglas Ianitsky
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('catalogrestrictiongroup');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('fvets_catalogrestrictiongroup')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('fvets_catalogrestrictiongroup')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('fvets_catalogrestrictiongroup')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('fvets_catalogrestrictiongroup')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('fvets_catalogrestrictiongroup')->__('Enabled'),
                            '0' => Mage::helper('fvets_catalogrestrictiongroup')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'website_id',
            array(
                'label'      => Mage::helper('fvets_catalogrestrictiongroup')->__('Change Website'),
                'url'        => $this->getUrl('*/*/massWebsiteId', array('_current'=>true)),
                'additional' => array(
                    'flag_website_id' => array(
                        'name'   => 'flag_website_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('fvets_catalogrestrictiongroup')->__('Website'),
                        'values' => Mage::getResourceModel('core/website_collection')->toOptionArray()

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
     * @param FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup
     * @return string
     * @author Douglas Ianitsky
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
     * @author Douglas Ianitsky
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Grid
     * @author Douglas Ianitsky
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
