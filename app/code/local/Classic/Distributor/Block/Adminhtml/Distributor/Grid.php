<?php
/**
 * Classic_Distributor extension
 *
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Distributor admin grid block
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Block_Adminhtml_Distributor_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('distributorGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Classic_Distributor_Block_Adminhtml_Distributor_Grid
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('classic_distributor/distributor')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Classic_Distributor_Block_Adminhtml_Distributor_Grid
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('classic_distributor')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('classic_distributor')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('classic_distributor')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('classic_distributor')->__('Enabled'),
                    '0' => Mage::helper('classic_distributor')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'telephone',
            array(
                'header' => Mage::helper('classic_distributor')->__('Telephone'),
                'index'  => 'telephone',
                'type'=> 'text',

            )
        );
        $this->addColumn(
            'website',
            array(
                'header' => Mage::helper('classic_distributor')->__('Website'),
                'index'  => 'website',
                'type'  => 'options',
                'options' => Mage::helper('classic_distributor')->convertOptions(
                    Mage::getModel('classic_distributor/attribute_source_website')->getAllOptions(false)
                )

            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('classic_distributor')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('classic_distributor')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('classic_distributor')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('classic_distributor')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('classic_distributor')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Classic_Distributor_Block_Adminhtml_Distributor_Grid
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('distributor');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('classic_distributor')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('classic_distributor')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('classic_distributor')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('classic_distributor')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('classic_distributor')->__('Enabled'),
                            '0' => Mage::helper('classic_distributor')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'website',
            array(
                'label'      => Mage::helper('classic_distributor')->__('Change Website'),
                'url'        => $this->getUrl('*/*/massWebsite', array('_current'=>true)),
                'additional' => array(
                    'flag_website' => array(
                        'name'   => 'flag_website',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('classic_distributor')->__('Website'),
                        'values' => Mage::getModel('classic_distributor/attribute_source_website')
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
     * @param Classic_Distributor_Model_Distributor
     * @return string
     * @author Douglas Borella Ianitsky
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
     * @author Douglas Borella Ianitsky
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return Classic_Distributor_Block_Adminhtml_Distributor_Grid
     * @author Douglas Borella Ianitsky
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
