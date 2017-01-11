<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Condition admin grid block
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Block_Adminhtml_Condition_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('conditionGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Condition_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('fvets_payment/condition')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Condition_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('fvets_payment')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('fvets_payment')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('fvets_payment')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('fvets_payment')->__('Enabled'),
                    '0' => Mage::helper('fvets_payment')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'start_days',
            array(
                'header' => Mage::helper('fvets_payment')->__('Start Date'),
                'index'  => 'start_days',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'split',
            array(
                'header' => Mage::helper('fvets_payment')->__('Split'),
                'index'  => 'split',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'split_range',
            array(
                'header' => Mage::helper('fvets_payment')->__('Split Range'),
                'index'  => 'split_range',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'price_range_begin',
            array(
                'header' => Mage::helper('fvets_payment')->__('Price Range Begin'),
                'index'  => 'price_range_begin',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'price_range_end',
            array(
                'header' => Mage::helper('fvets_payment')->__('Price Range End'),
                'index'  => 'price_range_end',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'apply_to_all',
            array(
                'header' => Mage::helper('fvets_payment')->__('Apply to all costumers'),
                'index'  => 'apply_to_all',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('fvets_payment')->__('Yes'),
                    '0' => Mage::helper('fvets_payment')->__('No'),
                )

            )
        );
		$this->addColumn(
			'category_id',
			array(
				'header'    => Mage::helper('fvets_payment')->__('Categories'),
				'index'     => 'category_id',
				'type'		=> 'text'
			)
		);
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'store_id',
                array(
                    'header'     => Mage::helper('fvets_payment')->__('Store Views'),
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
                'header'  =>  Mage::helper('fvets_payment')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('fvets_payment')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('fvets_payment')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('fvets_payment')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('fvets_payment')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Condition_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('condition');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('fvets_payment')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('fvets_payment')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('fvets_payment')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('fvets_payment')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('fvets_payment')->__('Enabled'),
                            '0' => Mage::helper('fvets_payment')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'apply_to_all',
            array(
                'label'      => Mage::helper('fvets_payment')->__('Change Apply to all costumers'),
                'url'        => $this->getUrl('*/*/massApplyToAll', array('_current'=>true)),
                'additional' => array(
                    'flag_apply_to_all' => array(
                        'name'   => 'flag_apply_to_all',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('fvets_payment')->__('Apply to all costumers'),
                        'values' => array(
                                '1' => Mage::helper('fvets_payment')->__('Yes'),
                                '0' => Mage::helper('fvets_payment')->__('No'),
                            )

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
     * @param FVets_Payment_Model_Condition
     * @return string
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
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Condition_Grid
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
     * @param FVets_Payment_Model_Resource_Condition_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return FVets_Payment_Block_Adminhtml_Condition_Grid
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
