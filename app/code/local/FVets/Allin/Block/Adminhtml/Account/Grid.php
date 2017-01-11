<?php

class FVets_Allin_Block_Adminhtml_Account_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('fvets_allin_adminhtml_account_grid');
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('fvets_allin/account')
			->getResourceCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header' => Mage::helper('fvets_allin')->__('ID'),
			'index' => 'id',
			'sortable' => false,
			'width' => '50',
		));

		$this->addColumn('name', array(
			'header' => Mage::helper('fvets_allin')->__('Name'),
			'index' => 'name',
			'sortable' => true,
		));

		$this->addColumn('website_id', array(
			'header' => Mage::helper('core')->__('Website ID'),
			'index' => 'website_id',
			'type' => 'website',
			'sortable' => true
		));

		$this->addColumn('user', array(
			'header' => Mage::helper('fvets_allin')->__('User'),
			'index' => 'user',
			'sortable' => true,
		));

//		$this->addColumn('password', array(
//			'header' => Mage::helper('fvets_allin')->__('Password'),
//			'index' => 'password',
//			'sortable' => true,
//		));

		$this->addColumn('list_name', array(
			'header' => Mage::helper('fvets_allin')->__('List Name'),
			'index' => 'list_name',
			'sortable' => true,
		));

		$this->addColumn('action', array(
			'header' => Mage::helper('fvets_allin')->__('Action'),
			'width' => '100',
			'type' => 'action',
			'getter' => 'getId',
			'actions' => array(
				array(
					'caption' => Mage::helper('fvets_allin')->__('Edit'),
					'url' => array('base' => '*/*/edit'),
					'field' => 'id'
				)
			),
			'filter' => false,
			'sortable' => false,
			'index' => 'action',
			'is_system' => true,
		));

		return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
		return $this->getUrl('*/*/*', array('_current' => true));
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}
