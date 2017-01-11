<?php

class FVets_Customdash_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Template
{
  var $activeCharts = array( //'Recompra (Beta)',
	'Faturamento',
	'Volume de Vendas',
	'Ticket Médio',
	'Valor total de Vendas por Representante',
	'Quantidade de Vendas por Representante',
	'Bestseller Marcas',
	'Bestseller Linhas',
	'Bestseller Subcategorias',
	'Bestseller SKUs',
	'Analytics');

  var $defaultDash = 'Faturamento';

  var $chartGroupDelimiters = array('Bestseller Marcas', 'Analytics');

  public function __construct()
  {
	parent::__construct();
	$this->setTemplate('fvets/customdash/info.phtml');
  }

  public function isGroupDelimiter($chartName) {
	return in_array($chartName, $this->chartGroupDelimiters);
  }
}
