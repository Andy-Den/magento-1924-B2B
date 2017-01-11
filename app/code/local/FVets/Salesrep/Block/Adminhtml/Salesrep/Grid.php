<?php

class FVets_Salesrep_Block_Adminhtml_Salesrep_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('fvets_salesrep_adminhtml_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('fvets_salesrep/salesrep')
            ->getResourceCollection();
        
		/**
		 * Permitir usuário ver os representantes somente das storeviews que possa acessar
		 */
		$permissions  = Mage::app()->getHelper('amrolepermissions')->currentRule()->getScopeStoreviews();
		if (isset($permissions))
			$collection->addFieldToFilter('store_id', array('in' => $permissions));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => Mage::helper('fvets_salesrep')->__('Id'),
                'index'  => 'id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('fvets_salesrep')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
            )
        );

		$this->addColumn(
			'email',
			array(
            	'header' => Mage::helper('fvets_salesrep')->__('E-mail'),
            	'index' => 'email',
            	'sortable' => true,
        	)
        );

		$this->addColumn(
			'telephone',
			array(
            	'header' => Mage::helper('fvets_salesrep')->__('Telephone'),
            	'index' => 'telephone',
            	'sortable' => true,
        	)
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('fvets_salesrep')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('fvets_salesrep')->__('Enabled'),
                    '0' => Mage::helper('fvets_salesrep')->__('Disabled'),
                )
            )
        );

		$this->addColumn(
			'id_erp',
			array(
				'header' => Mage::helper('fvets_salesrep')->__('Id on ERP'),
				'index' => 'id_erp',
				'sortable' => false,
			)
		);

		if (!Mage::app()->isSingleStoreMode())
		{
			$this->addColumn(
				'store_id',
				array(
					'header'        => Mage::helper('core')->__('Store View'),
					'index'         => 'store_id',
					'type'          => 'store',
					'store_all'     => true,
					'store_view'    => true,
					'sortable'      => true,
					'renderer'		=> 'FVets_Salesrep_Block_Adminhtml_Salesrep_Renderer_Storeview',
					'filter_condition_callback' => array(
						$this,
						'_filterStoreCondition'
					),
				)
			);
		}
        
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('fvets_salesrep')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('fvets_salesrep')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('fvets_salesrep')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('fvets_salesrep')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
                'index' => 'action',
            	'is_system' => true,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('fvets_salesrep')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('fvets_salesrep')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('fvets_salesrep')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('salesrep');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('fvets_salesrep')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('fvets_salesrep')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('fvets_salesrep')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('fvets_salesrep')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('fvets_salesrep')->__('Enabled'),
                            '0' => Mage::helper('fvets_salesrep')->__('Disabled'),
                        )
                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * get the row url
     *
     * @access public
     * @param FVets_Salesrep_Model_Salesrep
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _filterStoreCondition($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		$this->getCollection()->addStoreToFilter($value);

		return $this;
	}

    /**
     * after collection load
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Grid
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
