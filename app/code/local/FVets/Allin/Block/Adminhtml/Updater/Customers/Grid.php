<?php

class FVets_Allin_Block_Adminhtml_Updater_Customers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct()
	{
		parent::__construct();

		$this->setId('fvets_allin_adminhtml_updater_customers_grid');
		$this->setDefaultSort('updated_at');
		$this->setDefaultSort('desc');
		$this->setUseAjax(false);
		$this->setFilterVisibility(true);
	}

	protected function _prepareCollection()
	{
		$accountId = $this->getRequest()->getParam('account_switcher');
		if ($accountId) {
			$collection = Mage::helper('fvets_allin')->getCustomerList($accountId);
			$this->setCollection($collection);
		} else {
			//$this->setCollection(new Mage_Customer_Model_Resource_Customer_Collection());
			$this->setCollection(null);
		}
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('website_name', array(
			'header' => Mage::helper('core')->__('Distribuidora'),
			'index' => 'website_name',
			'sortable' => true
		));

		$this->addColumn('entity_id', array(
			'header' => Mage::helper('fvets_allin')->__('Customer ID'),
			'index' => 'entity_id',
			'sortable' => true,
			'width' => '50',
		));

		$this->addColumn('firstname', array(
			'header' => Mage::helper('fvets_allin')->__('Name'),
			'index' => 'firstname',
			'sortable' => true,
		));

		$this->addColumn('email', array(
			'header' => Mage::helper('fvets_allin')->__('Email'),
			'index' => 'email',
			'sortable' => true,
		));

		$this->addColumn('updated_at', array(
			'header' => Mage::helper('fvets_allin')->__('Updated Date'),
			'index' => 'updated_at',
			'sortable' => true,
		));

		$this->addColumn('razao_social', array(
			'header' => Mage::helper('fvets_allin')->__('Razão Social'),
			'index' => 'razao_social',
			'sortable' => true,
		));

		$this->addColumn('representante', array(
			'header' => Mage::helper('fvets_allin')->__('Representante'),
			'index' => 'representante',
			'sortable' => false,
		));

		$this->addColumn('telefone', array(
			'header' => Mage::helper('fvets_allin')->__('Telefone'),
			'index' => 'telefone',
			'sortable' => false,
		));

		$this->addColumn('cidade', array(
			'header' => Mage::helper('fvets_allin')->__('Cidade'),
			'index' => 'cidade',
			'sortable' => false,
		));

		$this->addColumn('estado', array(
			'header' => Mage::helper('fvets_allin')->__('Estado'),
			'index' => 'estado',
			'sortable' => false,
		));

		return parent::_prepareColumns();
	}

}
