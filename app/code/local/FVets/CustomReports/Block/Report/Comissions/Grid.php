<?php
class FVets_CustomReports_Block_Report_Comissions_Grid extends FVets_CustomReports_Block_Report_Comissions_Filter_Abstract {

	protected $_countTotals = true;

	public function __construct() {
		parent::__construct();

		$this->setId('gridreportcomissions');
		$this->setDefaultSort('created_at');
		$this->setDefaultSort('desc');
		$this->setUseAjax(false);
		$this->setFilterVisibility(true);
		$this->setEmptyText(Mage::helper('catalog')->__('There are no orders for this query.'));
		//verifica parâmetros de data (filtro)
		$this->checkFilters();
	}

	protected function _prepareCollection() {
		$dados = Mage::getModel( 'sales/order' )->getCollection()
		->addAttributeToSelect('customer_id')
			->addAttributeToSelect('customer_firstname')
			->addAttributeToSelect('increment_id')
			->addAttributeToSelect('store_name')
			->addAttributeToSelect('created_at')
			->addAttributeToSelect('grand_total');

		$dados->getSelect()
			->joinleft(array('customer' => 'customer_entity_varchar'), 'main_table.customer_id = customer.entity_id and customer.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'id_erp\' and entity_type_id = 1)', array('customer.value as id_erp'))
			->joinleft(array('customer2' => 'customer_entity_varchar'), 'main_table.customer_id = customer2.entity_id and customer2.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'razao_social\' and entity_type_id = 1)', array('customer2.value as razao_social'))
			->joinleft(array('customer3' => 'customer_entity_decimal'), 'main_table.customer_id = customer3.entity_id and customer3.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'commission\' and entity_type_id = 1)', array('customer3.value as comission'));

		$this->setWebsiteReportFilter($dados);
		$this->setStatusReportFilter($dados);
		$this->setDateFilter($dados);
		$this->setGenericFilter($dados);

		//echo $dados->getSelect()->__toString();

		$this->setCollection( $dados );
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn('increment_id', array(
			'header'=> $this->__('Nr. do Pedido'),
			'index' => 'increment_id',
			'sortable'  => true
		));
		$this->addColumn('id_erp', array(
			'header'=> $this->__('Cod. Cliente (Distribuidora)'),
			'index' => 'id_erp',
			'sortable'  => true
		));
		$this->addColumn('customer_firstname', array(
			'header'=> $this->__('Nome Cliente'),
			'index' => 'customer_firstname',
		));
		$this->addColumn('razao_social', array(
			'header'=> $this->__('Razão Social'),
			'index' => 'razao_social',
		));
		$this->addColumn('created_at', array(
			'header'=> $this->__('Data da Venda'),
			'index' => 'created_at',
			'type'  => 'date'
		));
		$this->addColumn('comission', array(
			'header'=> $this->__('Comissão'),
			'index' => 'comission',
			'type'  => 'number'
		));
		$this->addColumn('grand_total', array(
			'header'=> $this->__('Valor Total'),
			'index' => 'grand_total',
			'type'  => 'price',
			'currency_code' => Mage::app()->getStore(0)->getBaseCurrency()->getCode()
		));
		$this->addExportType('*/*/exportXls', Mage::helper('sales')->__('Excel XLS'));
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
		return parent::_prepareColumns();
	}

	public function getTotals()
	{
		$totals = new Varien_Object();
		$fields = array();
		$totalComission = 0;
		foreach ($this->getCollection() as $item) {
			$itemCustomer = Mage::getModel('customer/customer')->load($item->getCustomerId());
			$totalComission += (($item->getGrandTotal() * ($itemCustomer->getCommission() ? $itemCustomer->getCommission() : 0))/100);
		}

		$fields['grand_total'] = $totalComission;
		$totals->setData($fields);
		return $totals;
	}

	private function checkFilters() {
		$_websiteFilter = $this->getRequest()->getParam('website_switcher');
		$_fromDate = $this->getRequest()->getParam('date_from');
		$_toDate = $this->getRequest()->getParam('date_to');
		if (isset($_fromDate) && $_fromDate != '' && isset($_toDate) && $_toDate != '') {
			$this->setFromDate($_fromDate);
			$this->setToDate($_toDate);
		}

		$this->setWebsiteFilter($_websiteFilter);
	}
}

