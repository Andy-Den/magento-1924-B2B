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
 * Table Price admin grid block
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Block_Adminhtml_Tableprice_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('tablepriceGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return FVets_TablePrice_Block_Adminhtml_Tableprice_Grid
     * @author Douglas Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('fvets_tableprice/tableprice')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return FVets_TablePrice_Block_Adminhtml_Tableprice_Grid
     * @author Douglas Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('fvets_tableprice')->__('Id'),
				'width'   => '50',
                'index'  => 'entity_id',
                'type'   => 'number',
            )
        );

		$this->addColumn(
			'id_erp',
			array(
				'header' => Mage::helper('fvets_tableprice')->__('ID ERP'),
				'width'   => '50',
				'index'  => 'id_erp',
				'type'=> 'text',

			)
		);

        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('fvets_tableprice')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
            )
        );

		$this->addColumn(
			'discount',
			array(
				'header'    => Mage::helper('fvets_tableprice')->__('Discount'),
				'width'   => '50',
				'align'     => 'right',
				'index'     => 'discount',
			)
		);
        
        $this->addColumn(
            'customer_group_id',
            array(
                'header' => Mage::helper('fvets_tableprice')->__('Customer Group'),
                'index'  => 'customer_group_id',
                'type'  => 'options',
                'options' => Mage::helper('fvets_tableprice')->convertOptions(
                    Mage::getModel('customer/group')->getCollection()->addFieldToFilter('multiple_table', true)->toOptionArray()
                )

            )
        );

		$this->addColumn(
			'status',
			array(
				'header'  => Mage::helper('fvets_tableprice')->__('Status'),
				'width'   => '100',
				'index'   => 'status',
				'type'    => 'options',
				'options' => array(
					'1' => Mage::helper('fvets_tableprice')->__('Enabled'),
					'0' => Mage::helper('fvets_tableprice')->__('Disabled'),
				)
			)
		);

        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('fvets_tableprice')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('fvets_tableprice')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('fvets_tableprice')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('fvets_tableprice')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('fvets_tableprice')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return FVets_TablePrice_Block_Adminhtml_Tableprice_Grid
     * @author Douglas Ianitsky
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('tableprice');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('fvets_tableprice')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('fvets_tableprice')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('fvets_tableprice')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('fvets_tableprice')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('fvets_tableprice')->__('Enabled'),
                            '0' => Mage::helper('fvets_tableprice')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'customer_group_id',
            array(
                'label'      => Mage::helper('fvets_tableprice')->__('Change Customer Group ID'),
                'url'        => $this->getUrl('*/*/massCustomerGroupId', array('_current'=>true)),
                'additional' => array(
                    'flag_customer_group_id' => array(
                        'name'   => 'flag_customer_group_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('fvets_tableprice')->__('Customer Group ID'),
                        'values' => Mage::getModel('customer/group')->getCollection()->toOptionArray(),

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
     * @param FVets_TablePrice_Model_Tableprice
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
     * @return FVets_TablePrice_Block_Adminhtml_Tableprice_Grid
     * @author Douglas Ianitsky
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
