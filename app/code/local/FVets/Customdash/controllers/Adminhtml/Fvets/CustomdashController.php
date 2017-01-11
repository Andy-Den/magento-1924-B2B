<?php

class FVets_Customdash_Adminhtml_Fvets_CustomdashController extends Mage_Adminhtml_Controller_Action
{

  public function indexAction()
  {
	$this->loadLayout();
	$this->_setActiveMenu('customer/fvets_customdash');
	$this->renderLayout();
  }

  public function chartAction()
  {
	$_chartName = $this->getRequest()->getParam('name');

	switch ($_chartName)
	{
	  case 'Faturamento':
		echo $this->getFaturamentoChartHtml();
		break;
	  case 'Recompra (Beta)':
		echo $this->getRecompraChartHtml();
		break;
	  case 'Volume de Vendas':
		echo $this->getVolumeVendasChartHtml();
		break;
	  case 'Ticket Médio':
		echo $this->getTicketMedioChartHtml();
		break;
	  case 'Valor total de Vendas por Representante':
			echo $this->getTotalValueVendasRepChartHtml();
			break;
	  case 'Quantidade de Vendas por Representante':
		echo $this->getQtdVendasRepChartHtml();
		break;
	  case 'Bestseller Linhas':
		echo $this->getBestsellerLinhasChartHtml();
		break;
	  case 'Bestseller Marcas':
		echo $this->getBestsellerMarcasChartHtml();
		break;
	  case 'Bestseller Subcategorias':
		echo $this->getBestsellerSubcategoriasChartHtml();
		break;
	  case 'Bestseller SKUs':
		echo $this->getBestsellerSkusChartHtml();
		break;
	  case 'Analytics':
		echo $this->getAnalyticsDashboard();
		break;
	}
  }

  public function getStoresAction()
  {
	$websiteCode = Mage::app()->getRequest()->getParam('code');
	$block = $this->getLayout()->createBlock('fvets_customdash/adminhtml_switcher_storeswitcher')->setIdWebsite($websiteCode);
	echo $block->toHtml();
  }

  private function getFaturamentoChartHtml()
  {
	$activeCharts = array(
	  array('type' => 'column', 'params' => array('name' => array('name', 'Faturamento'), 'id' => array('id', 'sales_vlr_column'), 'columns' => array('columns', array(array('string', 'Marca'), array('number', 'Valor'))), 'methodData' => array('methodData', 'getTotalValueSales'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Faturamento\', titleTextStyle: {color: \'red\'}}'), array('width', '590'), array('height', '400'))), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'donut', 'params' => array('name' => array('name', '...'), 'id' => array('id', 'sales_vlr_donut'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Value'))), 'methodData' => array('methodData', 'getTotalValueSales'), 'width' => array('width', 500), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'table', 'params' => array('name' => array('name', ''), 'id' => array('id', 'sales_vlr_table'), 'columns' => array('columns', array(array('string', 'Mês'), array('number', 'Total'))), 'methodData' => array('methodData', 'getTotalValueSales'), 'width' => array('width', 500), 'height' => array('height', 300), 'tipoData' => array('tipoData', 'decimal'))));
	return $this->drawChart($activeCharts);
  }

  private function getRecompraChartHtml()
  {
	$activeCharts = array(
	  array('type' => 'column', 'params' => array('name' => array('name', 'Recompra'), 'id' => array('id', 'sales_firstbuy_column'), 'columns' => array('columns', array(array('string', 'Ano/Mês'), array('number', 'Primeira Compra'))), 'methodData' => array('methodData', 'getRebuyFirstTimeSales'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Recompra (%)\', titleTextStyle: {color: \'red\'}}'), array('width', '494'), array('height', '400'))))),
	  array('type' => 'column', 'params' => array('name' => array('name', 'Clientes que Compraram (%)'), 'id' => array('id', 'sales_customers_bought_column'), 'columns' => array('columns', array(array('string', 'Ano/Mês'), array('number', '% Compraram'))), 'methodData' => array('methodData', 'getCustomersBought'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Clientes que Compraram (%)\', titleTextStyle: {color: \'red\'}}'), array('width', '494'), array('height', '400'))))),
	  array('type' => 'column', 'params' => array('name' => array('name', 'Recompra'), 'id' => array('id', 'sales_rebuy_column'), 'columns' => array('columns', array(array('string', 'Ano/Mês'), array('number', 'Segunda Compra'), array('number', 'Terceira Compra'), array('number', 'Quarta ou Mais Compras'))), 'methodData' => array('methodData', 'getRebuySales'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Recompra (%)\', titleTextStyle: {color: \'red\'}}'), array('width', '1021'), array('height', '400')))))
	);
	return $this->drawChart($activeCharts);
  }

  private function getVolumeVendasChartHtml()
  {
	$activeCharts = array(
	  //quantidade total de vendas
	  array('type' => 'column', 'params' => array('name' => array('name', 'Volume de Vendas'), 'id' => array('id', 'sales_vol_column'), 'columns' => array('columns', array(array('string', 'Marca'), array('number', 'Qtd. Pedidos'))), 'methodData' => array('methodData', 'getTotalVolumeSales'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Volume de Vendas\', titleTextStyle: {color: \'red\'}}'), array('width', '590'), array('height', '400'))))),
	  array('type' => 'donut', 'params' => array('name' => array('name', '...'), 'id' => array('id', 'sales_vol_donut'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Value'))), 'methodData' => array('methodData', 'getTotalVolumeSales'), 'width' => array('width', 500))),
	  array('type' => 'table', 'params' => array('name' => array('name', ''), 'id' => array('id', 'sales_vol_table'), 'columns' => array('columns', array(array('string', 'Mês'), array('number', 'Total'))), 'methodData' => array('methodData', 'getTotalVolumeSales'), 'width' => array('width', 500), 'height' => array('height', 300))));
	return $this->drawChart($activeCharts);
  }

  private function getTicketMedioChartHtml()
  {
	$activeCharts = array(
	  //ticket médio
	  array('type' => 'column', 'params' => array('name' => array('name', 'Ticket Médio'), 'id' => array('id', 'sales_ticket_column'), 'columns' => array('columns', array(array('string', 'Mês'), array('number', 'Ticket Médio'))), 'methodData' => array('methodData', 'getAverageTicketSales'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Ticket Médio\', titleTextStyle: {color: \'red\'}}'), array('width', '590'), array('height', '400'))), 'tipoData' => array('tipoData', 'decimal'), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'donut', 'params' => array('name' => array('name', '...'), 'id' => array('id', 'sales_ticket_donut'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Value'))), 'methodData' => array('methodData', 'getAverageTicketSales'), 'width' => array('width', 500), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'table', 'params' => array('name' => array('name', ''), 'id' => array('id', 'sales_ticket_table'), 'columns' => array('columns', array(array('string', 'Mês'), array('number', 'Total'))), 'methodData' => array('methodData', 'getAverageTicketSales'), 'width' => array('width', 500), 'height' => array('height', 300), 'tipoData' => array('tipoData', 'decimal'))));
	return $this->drawChart($activeCharts);
  }

	private function getTotalValueVendasRepChartHtml()
	{
		$activeCharts = array(
			//vendas por representante
			array('type' => 'column', 'params' => array('name' => array('name', 'Valor Total de Vendas por Rep.'), 'id' => array('id', 'sales_vlr_rep_column'), 'columns' => array('columns', array(array('string', 'Representante'), array('number', 'Valor total de Vendas'))), 'methodData' => array('methodData', 'getTotalValueSalesRep'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Valor total de Vendas por Representante\', titleTextStyle: {color: \'red\'}, textStyle: {fontSize: 9}}'), array('width', '590'), array('height', '400'))), 'noDataLabel' => array('noDataLabel', 'Cli sem rep vinculado'), 'tipoData' => array('tipoData', 'decimal'))),
			array('type' => 'donut', 'params' => array('name' => array('name', '...'), 'id' => array('id', 'sales_vlr_rep_donut'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Value'))), 'methodData' => array('methodData', 'getTotalValueSalesRep'), 'width' => array('width', 500), 'noDataLabel' => array('noDataLabel', 'Cli sem rep vinculado'), 'tipoData' => array('tipoData', 'decimal'))),
			array('type' => 'table', 'params' => array('name' => array('name', ''), 'id' => array('id', 'sales_vlr_rep_table'), 'columns' => array('columns', array(array('string', 'Representante'), array('number', 'Valor total de Vendas'))), 'methodData' => array('methodData', 'getTotalValueSalesRep'), 'width' => array('width', 500), 'height' => array('height', 300), 'noDataLabel' => array('noDataLabel', 'Cli sem rep vinculado'), 'tipoData' => array('tipoData', 'decimal'))));
		return $this->drawChart($activeCharts);
	}

  private function getQtdVendasRepChartHtml()
  {
	$activeCharts = array(
	  //vendas por representante
	  array('type' => 'column', 'params' => array('name' => array('name', 'Qtd. de Vendas por Rep.'), 'id' => array('id', 'sales_qty_rep_column'), 'columns' => array('columns', array(array('string', 'Representante'), array('number', 'Qtd. Vendas'))), 'methodData' => array('methodData', 'getQtySalesRep'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Quantidade de Vendas por Representante\', titleTextStyle: {color: \'red\'}, textStyle: {fontSize: 9}}'), array('width', '590'), array('height', '400'))), 'noDataLabel' => array('noDataLabel', 'Cli sem rep vinculado'))),
	  array('type' => 'donut', 'params' => array('name' => array('name', '...'), 'id' => array('id', 'sales_qty_rep_donut'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Value'))), 'methodData' => array('methodData', 'getQtySalesRep'), 'width' => array('width', 500), 'noDataLabel' => array('noDataLabel', 'Cli sem rep vinculado'))),
	  array('type' => 'table', 'params' => array('name' => array('name', ''), 'id' => array('id', 'sales_qty_rep_table'), 'columns' => array('columns', array(array('string', 'Representante'), array('number', 'Qtd. Vendas'))), 'methodData' => array('methodData', 'getQtySalesRep'), 'width' => array('width', 500), 'height' => array('height', 300), 'noDataLabel' => array('noDataLabel', 'Cli sem rep vinculado'))));
	return $this->drawChart($activeCharts);
  }

  private function getBestsellerMarcasChartHtml()
  {
	$activeCharts = array(
	  //marcas mais vendidas
	  array('type' => 'column', 'params' => array('name' => array('name', 'Bestseller Marcas'), 'id' => array('id', 'best_seller_brands_column'), 'columns' => array('columns', array(array('string', 'Marca'), array('number', 'Valor total dos Produtos'))), 'methodData' => array('methodData', 'getBestSellerBrands'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Marcas\', titleTextStyle: {color: \'red\'}}'), array('width', '590'), array('height', '400'))), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'donut', 'params' => array('name' => array('name', '...'), 'id' => array('id', 'best_seller_brands_donut'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Value'))), 'methodData' => array('methodData', 'getBestSellerBrands'), 'width' => array('width', 370), 'height' => array('height', 300), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'table', 'params' => array('name' => array('name', ''), 'id' => array('id', 'best_seller_brands_table'), 'columns' => array('columns', array(array('string', 'Marca'), array('number', 'Qtd. Produtos'))), 'methodData' => array('methodData', 'getBestSellerBrands'), 'width' => array('width', 370), 'height' => array('height', 300), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))));
	return $this->drawChart($activeCharts);
  }

  private function getBestsellerLinhasChartHtml()
  {
	$activeCharts = array(
	  //marcas mais vendidas
	  array('type' => 'column', 'params' => array('name' => array('name', 'Bestseller Linhas'), 'id' => array('id', 'best_seller_linhas_column'), 'columns' => array('columns', array(array('string', 'Linha'), array('number', 'Valor total dos Produtos'))), 'methodData' => array('methodData', 'getBestSellerLinhas'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Linhas\', titleTextStyle: {color: \'red\'}}'), array('width', '590'), array('height', '400'))), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'donut', 'params' => array('name' => array('name', '...'), 'id' => array('id', 'best_seller_linhas_donut'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Value'))), 'methodData' => array('methodData', 'getBestSellerLinhas'), 'width' => array('width', 370), 'height' => array('height', 300), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'table', 'params' => array('name' => array('name', ''), 'id' => array('id', 'best_seller_linhas_table'), 'columns' => array('columns', array(array('string', 'Linha'), array('number', 'Qtd. Produtos'))), 'methodData' => array('methodData', 'getBestSellerLinhas'), 'width' => array('width', 370), 'height' => array('height', 300), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))));
	return $this->drawChart($activeCharts);
  }

  private function getBestsellerSubcategoriasChartHtml()
  {
	$activeCharts = array(
	  //sub-categorias mais vendidas
	  array('type' => 'column', 'params' => array('name' => array('name', 'Bestseller Subcategorias'), 'id' => array('id', 'best_seller_subcategories_column'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Valor total dos Produtos'))), 'methodData' => array('methodData', 'getBestSellerSubcategories'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'Subcategorias\', titleTextStyle: {color: \'red\'}}'), array('width', '590'), array('height', '400'))), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'donut', 'params' => array('name' => array('name', '...'), 'id' => array('id', 'best_seller_subcategories_donut'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Value'))), 'methodData' => array('methodData', 'getBestSellerSubcategories'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('pieSliceText', 'label'))), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))),
	  array('type' => 'table', 'params' => array('name' => array('name', ''), 'id' => array('id', 'best_seller_subcategories_table'), 'columns' => array('columns', array(array('string', 'Subcategoria'), array('number', 'Qtd. Produtos'))), 'methodData' => array('methodData', 'getBestSellerSubcategories'), 'width' => array('width', 370), 'height' => array('height', 300), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))));
	return $this->drawChart($activeCharts);
  }

  private function getBestsellerSkusChartHtml()
  {
	$activeCharts = array(
	  //SKUs mais vendidos
//            array('type' => 'column', 'params' => array('name' => array('name', 'Bestseller SKUs'), 'id' => array('id', 'best_seller_skus_column'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Qtd. Produtos'))), 'methodData' => array('methodData', 'getBestSellerSkus'), 'width' => array('width', 370), 'height' => array('height', 300), 'options' => array('options', array(array('hAxis', '{title: \'SKUs\', titleTextStyle: {color: \'red\'}}'), array('width', '1014'), array('height', '400'))), 'noDataLabel' => array('noDataLabel', '- - vazio - -'))))
	  array('type' => 'donut', 'params' => array('name' => array('name', 'Produtos Mais Vendidos'), 'id' => array('id', 'best_seller_skus_donut'), 'columns' => array('columns', array(array('string', 'Label'), array('number', 'Value'))), 'methodData' => array('methodData', 'getBestSellerSkus'), 'options' => array('options', array(array('pieSliceText', 'value'), array('width', 1014), array('height', 500), array('title', 'Valor total dos Produtos'))), 'noDataLabel' => array('noDataLabel', '- - vazio - -'), 'tipoData' => array('tipoData', 'decimal'))));
//            array('type' => 'table', 'params' => array('name' => array('name', ''), 'id' => array('id', 'best_seller_skus_table'), 'columns' => array('columns', array(array('string', 'Produto'), array('number', 'Qtd. Produtos'))), 'methodData' => array('methodData', 'getBestSellerSkus'), 'width' => array('width', 370), 'height' => array('height', 300), 'noDataLabel' => array('noDataLabel', '- - vazio - -'))));
	return $this->drawChart($activeCharts);
  }

  private function getAnalyticsDashboard()
  {
	$analyticsBlock = Mage::getSingleton('core/layout')->createBlock('fvets_customdash/adminhtml_analytics_analytics');
	$analyticsBlock->toHtml();

	return $analyticsBlock->toHtml();
  }

  private function drawChart($activeCharts)
  {
	$_returnHtml = '';
	foreach ($activeCharts as $_chartParams)
	{
	  $_chart = Mage::getSingleton('core/layout')->createBlock('fvets_customdash/adminhtml_charts_' . $_chartParams['type'], $_chartParams['params']['id'][1]);

	  foreach ($_chartParams['params'] as $param)
	  {
		$function = 'set' . ucfirst($param[0]);
		$_chart->$function($param[1]);
	  }
	  //$this->setChild($_chartParams['params']['id'][1], $_chart);
	  $_returnHtml .= $_chart->toHtml();
	}
	return $_returnHtml;
  }

  protected function _isAllowed()
  {
	return Mage::getSingleton('admin/session')->isAllowed('admin/customdash');
  }
}
