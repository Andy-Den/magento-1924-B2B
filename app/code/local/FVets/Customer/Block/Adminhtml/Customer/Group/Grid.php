<?php

class FVets_Customer_Block_Adminhtml_Customer_Group_Grid extends Mage_Adminhtml_Block_Customer_Group_Grid
{

	public function __construct()
	{
		parent::__construct();
		$this->setDefaultSort('time');
		$this->setDefaultDir('desc');
	}

	/**
	 * Init customer groups collection
	 * @return void
	 */
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('customer/group_collection')
			->addWebsiteFilter()
			->addTaxClass();

		$this->setCollection($collection);
		//return parent::_prepareCollection();
		return call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareCollection'));
	}

	/**
	 * Configuration of grid
	 */
	protected function _prepareColumns()
	{
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumnAfter('website_id', array(
				'header'    => Mage::helper('customer')->__('Website'),
				'align'     => 'left',
				'type'      => 'options',
				'width'		=> '200px',
				'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
				'index'     => 'website_id',
			), 'type');
		}

		$this->addColumnAfter('id_tabela', array(
			'header' => Mage::helper('customer')->__('Table ID'),
			'width'	 => '200px',
			'index'  => 'id_tabela',
		), 'website_id');

		$this->addColumnAfter('multiple_table', array(
			'header'    => Mage::helper('customer')->__('Multiple Table'),
			'align'     => 'left',
			'type'      => 'options',
			'width'		=> '80px',
			'options'   => Mage::getModel('adminhtml/system_config_source_yesno')->toArray(),
			'index'     => 'multiple_table',
		), 'id_tabela');

		return parent::_prepareColumns();
	}
}