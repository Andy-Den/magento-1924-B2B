<?php

class FVets_Customdash_Block_Adminhtml_Business extends FVets_Customdash_Block_Adminhtml_Filters
{
  public function __construct()
  {
	parent::__construct();

	//verifica parâmetros de data (filtro)
	$_fromDate = $this->getRequest()->getParam('date_from');
	$_toDate = $this->getRequest()->getParam('date_to');
	if (isset($_fromDate) && $_fromDate != '' && isset($_toDate) && $_toDate != '')
	{
	  $this->setFromDate($_fromDate);
	  $this->setToDate($_toDate);
	}

	$_webSiteFilter = $this->getRequest()->getParam('website_switcher');
	if (isset($_webSiteFilter))
	{
	  $this->setWebsiteFilter($_webSiteFilter);
	}
  }

  public function getQtySalesRep()
  {
	$collection = Mage::getModel('sales/order')
	  ->getCollection()
	  ->addAttributeToSelect('created_at'); //precisava de um valor único nesse campo

	$this->setDateFilter($collection);
	$this->setStatusFilter($collection);
	$this->setCurrentWebsiteFilter($collection);
	$this->setCurrentStoreviewFilter($collection);
	$this->setGenericFilter($collection);

	$collection->getSelect()->joinInner(array('sfoi' => 'sales_flat_order_item'), 'sfoi.order_id = main_table.entity_id', 'sfoi.salesrep_id')
	  ->joinInner(array('fs' => 'fvets_salesrep'), 'fs.id = sfoi.salesrep_id', 'fs.name as label')
	  ->columns('COUNT(DISTINCT main_table.entity_id) as value')
	  ->group('sfoi.salesrep_id')
	  ->group('fs.name')
	  ->order('COUNT(DISTINCT main_table.entity_id) desc');

	//$_query = $collection->getSelect()->__toString();
	//echo $_query;

	$this->setCollection($collection);
	return $collection;
  }


  public function getTotalValueSalesRep()
  {
	  $collection = Mage::getModel('sales/order')
		  ->getCollection()
		  ->addAttributeToSelect('created_at'); //precisava de um valor único nesse campo

	  $this->setDateFilter($collection);
	  $this->setStatusFilter($collection);
	  $this->setCurrentWebsiteFilter($collection);
	  $this->setCurrentStoreviewFilter($collection);
	  $this->setGenericFilter($collection);

	  $collection->getSelect()->joinInner(array('sfoi' => 'sales_flat_order_item'), 'sfoi.order_id = main_table.entity_id', 'sfoi.salesrep_id')
		  ->joinInner(array('fs' => 'fvets_salesrep'), 'fs.id = sfoi.salesrep_id', 'fs.name as label')
		  ->columns('sum(sfoi.base_row_total) as value')
		  ->group('sfoi.salesrep_id')
		  ->group('fs.name')
		  ->order('sum(sfoi.base_row_total) desc');

	  //$_query = $collection->getSelect()->__toString();
	  //echo $_query;

	  $this->setCollection($collection);
	  return $collection;
  }

  public function getBestSellerBrands()
  {
	$collection = Mage::getModel('sales/order')
	  ->getCollection()
	  ->addAttributeToSelect('customer_id');

	$this->setDateFilter($collection);
	$this->setStatusFilter($collection);
	$this->setCurrentWebsiteFilter($collection);
	$this->setCurrentStoreviewFilter($collection);
	$this->setGenericFilter($collection);


	$collection->getSelect()->join(array('sfoi' => 'sales_flat_order_item'), 'main_table.entity_id = sfoi.order_id', array('sum(sfoi.base_row_total) as value'))
	  ->join(array('ccp' => 'catalog_category_product'), 'ccp.product_id = sfoi.product_id', array(''));
	$excludedCategories = Mage::getStoreConfig('customdash/bestsellerbrands/excluded_brands_ids');
	if ($this->getWebsiteFilter())
	{
	  $website = Mage::getModel('core/website')->load($this->getWebsiteFilter());
	  $categoryPathString = $this->getCategoryPathFilterStringByWebsite($website);
	  $collection->getSelect()->join(array('cce' => 'catalog_category_entity'), 'cce.entity_id = ccp.category_id and cce.`level` in (2) and path not like "1/2/%"' . $categoryPathString, array(''));
	} else
	{
	  $collection->getSelect()->join(array('cce' => 'catalog_category_entity'), 'cce.entity_id = ccp.category_id and cce.`level` in (2) and path not like "1/2/%"', array(''));
	}
	$collection->getSelect()->join(array('ccev' => 'catalog_category_entity_varchar'), 'ccev.entity_id = ccp.category_id ' . ($excludedCategories ? (' and ccp.category_id not in (' . $excludedCategories . ') ') : '') . 'and ccev.attribute_id = (select ea.attribute_id from eav_attribute ea where ea.attribute_code = \'name\' and ea.entity_type_id = 3)', array('replace(ccev.value, "\'", "") as label'))
	  //->distinct(true);
	  ->group('ccev.value')
	  ->order('sum(sfoi.base_row_total) desc')
	  ->limit('10');

//		$_query = $collection->getSelect()->__toString();
//		echo $_query;

	$this->setCollection($collection);
	return $collection;
  }

  public function getBestSellerLinhas()
  {
	$collection = Mage::getModel('sales/order')
	  ->getCollection()
	  ->addAttributeToSelect('customer_id');

	$this->setDateFilter($collection);
	$this->setStatusFilter($collection);
	$this->setCurrentWebsiteFilter($collection);
	$this->setCurrentStoreviewFilter($collection);
	$this->setGenericFilter($collection);

	$collection->getSelect()->join(array('sfoi' => 'sales_flat_order_item'), 'main_table.entity_id = sfoi.order_id', array('sum(sfoi.base_row_total) as value'))
	  ->join(array('ccp' => 'catalog_category_product'), 'ccp.product_id = sfoi.product_id', array(''));

	$excludedCategories = Mage::getStoreConfig('customdash/bestsellerlines/excluded_lines_ids');
	if ($this->getWebsiteFilter())
	{
	  $website = Mage::getModel('core/website')->load($this->getWebsiteFilter());
	  $categoryPathString = $this->getCategoryPathFilterStringByWebsite($website);
	  $collection->getSelect()->join(array('cce' => 'catalog_category_entity'), 'cce.entity_id = ccp.category_id and cce.`level` in (3) and path not like "1/2/%"' . $categoryPathString, array(''));
	} else
	{
	  $collection->getSelect()->join(array('cce' => 'catalog_category_entity'), 'cce.entity_id = ccp.category_id and cce.`level` in (3) and path not like "1/2/%"', array(''));
	}
	$collection->getSelect()->join(array('ccev' => 'catalog_category_entity_varchar'), 'ccev.entity_id = ccp.category_id ' . ($excludedCategories ? (' and ccp.category_id not in (' . $excludedCategories . ') ') : '') . 'and ccev.attribute_id = (select ea.attribute_id from eav_attribute ea where ea.attribute_code = \'name\' and ea.entity_type_id = 3)', array('replace(ccev.value, "\'", "") as label'))
	  //->distinct(true);
	  ->group('ccev.value')
	  ->order('sum(sfoi.base_row_total) desc')
	  ->limit('10');

//		$_query = $collection->getSelect()->__toString();
//		echo $_query;

	$this->setCollection($collection);
	return $collection;
  }

  public function getBestSellerSubcategories()
  {
	$collection = Mage::getModel('sales/order')
	  ->getCollection()
	  ->addAttributeToSelect('customer_id');

	$this->setDateFilter($collection);
	$this->setStatusFilter($collection);
	$this->setCurrentWebsiteFilter($collection);
	$this->setCurrentStoreviewFilter($collection);
	$this->setGenericFilter($collection);

	$collection->getSelect()->join(array('sfoi' => 'sales_flat_order_item'), 'main_table.entity_id = sfoi.order_id', array('sum(sfoi.base_row_total) as value'))
	  ->join(array('ccp' => 'catalog_category_product'), 'ccp.product_id = sfoi.product_id', array(''));

	$excludedCategories = Mage::getStoreConfig('customdash/bestsellersubcategories/excluded_subcategories_ids');
	if ($this->getWebsiteFilter())
	{
	  $website = Mage::getModel('core/website')->load($this->getWebsiteFilter());
	  $categoryPathString = $this->getCategoryPathFilterStringByWebsite($website);
	  $collection->getSelect()->join(array('cce' => 'catalog_category_entity'), 'cce.entity_id = ccp.category_id and cce.`level` in (4) and path not like "1/2/%"' . $categoryPathString, array(''));
	} else
	{
	  $collection->getSelect()->join(array('cce' => 'catalog_category_entity'), 'cce.entity_id = ccp.category_id and cce.`level` in (4) and path not like "1/2/%"', array(''));
	}
	  $collection->getSelect()->join(array('ccev' => 'catalog_category_entity_varchar'), 'ccev.entity_id = ccp.category_id ' . ($excludedCategories ? (' and ccp.category_id not in (' . $excludedCategories . ') ') : '') . 'and ccev.attribute_id = (select ea.attribute_id from eav_attribute ea where ea.attribute_code = \'name\' and ea.entity_type_id = 3)', array('replace(ccev.value, "\'", "") as label'))
	  //->distinct(true);
	  ->group('ccev.value')
	  ->order('sum(sfoi.base_row_total) desc')
	  ->limit('10');

//	$_query = $collection->getSelect()->__toString();
//	echo $_query;

	$this->setCollection($collection);
	return $collection;
  }

  public function getBestSellerSkus()
  {
	$collection = Mage::getModel('sales/order')
	  ->getCollection()
	  ->addAttributeToSelect('customer_id');

	$this->setDateFilter($collection);
	$this->setStatusFilter($collection);
	$this->setCurrentWebsiteFilter($collection);
	$this->setCurrentStoreviewFilter($collection);
	$this->setGenericFilter($collection);

	$collection->getSelect()->join(array('sfoi' => 'sales_flat_order_item'), 'main_table.entity_id = sfoi.order_id', array('sum(sfoi.base_row_total) as value'))
	  ->join(array('cpev' => 'catalog_product_entity_varchar'), 'cpev.entity_id = sfoi.product_id and cpev.attribute_id = (select ea.attribute_id from eav_attribute ea where ea.attribute_code = \'name\' and ea.entity_type_id = 4) and cpev.store_id = 0', array('replace(concat(cpev.value, " (", ccev.value, ")"), "\'", "") AS label'))
	  ->join(array('ccp' => 'catalog_category_product'), 'ccp.product_id = sfoi.product_id', array(''));
	if ($this->getWebsiteFilter())
	{
	  $website = Mage::getModel('core/website')->load($this->getWebsiteFilter());
	  $categoryPathString = $this->getCategoryPathFilterStringByWebsite($website);
	  $collection->getSelect()->join(array('cce' => 'catalog_category_entity'), 'cce.entity_id = ccp.category_id and cce.`level` in (2) and path not like "1/2/%"' . $categoryPathString, array(''));
	} else
	{
	  $collection->getSelect()->join(array('cce' => 'catalog_category_entity'), 'cce.entity_id = ccp.category_id and cce.`level` in (2)', array(''));
	}
	$collection->getSelect()->join(array('ccev' => 'catalog_category_entity_varchar'), 'ccev.entity_id = ccp.category_id and ccp.category_id not in (2, 3) and ccev.attribute_id = (select ea.attribute_id from eav_attribute ea where ea.attribute_code = \'name\' and ea.entity_type_id = 3)', array(''))
	  //->distinct(true);
	  ->group('cpev.value')
	  ->order('sum(sfoi.base_row_total) desc')
	  ->limit('10');

//		$_query = $collection->getSelect()->__toString();
//		echo $_query;

	$this->setCollection($collection);
	return $collection;
  }

  public function getTotalVolumeSales()
  {
	$collection = Mage::getModel('sales/order')
	  ->getCollection()
	  ->addAttributeToSelect('entity_id');

	$this->setDateFilter($collection);
	$this->setStatusFilter($collection);
	$this->setCurrentWebsiteFilter($collection);
	$this->setCurrentStoreviewFilter($collection);
	$this->setGenericFilter($collection);

	$collection->getSelect()->columns('CASE WHEN MONTH(created_at) = 1 THEN \'Janeiro\' WHEN MONTH(created_at) = 2 THEN \'Fevereiro\' WHEN MONTH(created_at) = 3 THEN \'Março\' WHEN MONTH(created_at) = 4 THEN \'Abril\' WHEN MONTH(created_at) = 5 THEN \'Maio\' WHEN MONTH(created_at) = 6 THEN \'Junho\' WHEN MONTH(created_at) = 7 THEN \'Julho\' WHEN MONTH(created_at) = 8 THEN \'Agosto\' WHEN MONTH(created_at) = 9 THEN \'Setembro\' WHEN MONTH(created_at) = 10 THEN \'Outubro\' WHEN MONTH(created_at) = 11 THEN \'Novembro\' WHEN MONTH(created_at) = 12 THEN \'Dezembro\' ELSE MONTH(created_at) END as label')
	  ->columns('count(base_grand_total) as value');
	$collection->getSelect()->group("month(convert_tz(created_at, 'UTC', 'America/Sao_Paulo'))")
	  ->order('year(created_at) asc, month(created_at) asc');

	//$_query = $collection->getSelect()->__toString();
	//echo $_query;

	$this->setCollection($collection);
	return $collection;
  }

  public function getTotalValueSales()
  {
	$collection = Mage::getModel('sales/order')
	  ->getCollection()
	  ->addAttributeToSelect('entity_id');

	$this->setDateFilter($collection);
	$this->setStatusFilter($collection);
	$this->setCurrentWebsiteFilter($collection);
	$this->setCurrentStoreviewFilter($collection);
	$this->setGenericFilter($collection);

	$collection->getSelect()->columns('CASE WHEN MONTH(created_at) = 1 THEN \'Janeiro\' WHEN MONTH(created_at) = 2 THEN \'Fevereiro\' WHEN MONTH(created_at) = 3 THEN \'Março\' WHEN MONTH(created_at) = 4 THEN \'Abril\' WHEN MONTH(created_at) = 5 THEN \'Maio\' WHEN MONTH(created_at) = 6 THEN \'Junho\' WHEN MONTH(created_at) = 7 THEN \'Julho\' WHEN MONTH(created_at) = 8 THEN \'Agosto\' WHEN MONTH(created_at) = 9 THEN \'Setembro\' WHEN MONTH(created_at) = 10 THEN \'Outubro\' WHEN MONTH(created_at) = 11 THEN \'Novembro\' WHEN MONTH(created_at) = 12 THEN \'Dezembro\' ELSE MONTH(created_at) END as label')
	  ->columns('sum(base_grand_total) as value');
	$collection->getSelect()->group("month(convert_tz(created_at, 'UTC', 'America/Sao_Paulo'))")
	  ->order('year(created_at) asc, month(created_at) asc');


//		$_query = $collection->getSelect()->__toString();
//		echo $_query;

	$this->setCollection($collection);
	return $collection;
  }

  public function getAverageTicketSales()
  {
	$collection = Mage::getModel('sales/order')
	  ->getCollection()
	  ->addAttributeToSelect('entity_id');

	$this->setDateFilter($collection);
	$this->setStatusFilter($collection);
	$this->setCurrentWebsiteFilter($collection);
	$this->setCurrentStoreviewFilter($collection);
	$this->setGenericFilter($collection);

	$collection->getSelect()->columns('CASE WHEN MONTH(created_at) = 1 THEN \'Janeiro\' WHEN MONTH(created_at) = 2 THEN \'Fevereiro\' WHEN MONTH(created_at) = 3 THEN \'Março\' WHEN MONTH(created_at) = 4 THEN \'Abril\' WHEN MONTH(created_at) = 5 THEN \'Maio\' WHEN MONTH(created_at) = 6 THEN \'Junho\' WHEN MONTH(created_at) = 7 THEN \'Julho\' WHEN MONTH(created_at) = 8 THEN \'Agosto\' WHEN MONTH(created_at) = 9 THEN \'Setembro\' WHEN MONTH(created_at) = 10 THEN \'Outubro\' WHEN MONTH(created_at) = 11 THEN \'Novembro\' WHEN MONTH(created_at) = 12 THEN \'Dezembro\' ELSE MONTH(created_at) END as label')
	  ->columns('ROUND(sum(base_grand_total)/count(base_grand_total), 2) as value');
	$collection->getSelect()->group("month(convert_tz(created_at, 'UTC', 'America/Sao_Paulo'))")
	  ->order('year(created_at) asc, month(created_at) asc');


//		$_query = $collection->getSelect()->__toString();
//		echo $_query;

	$this->setCollection($collection);
	return $collection;
  }

  public function getRebuySales()
  {

	//formatando a lista de emails teste para removermos da query;
	$_testUsers = "'" . $this->testUsers[0] . "'";

	$query = 'SELECT vrod.mesCompra as col1,
round((100*(
SELECT SUM(vrod2.quantidadeOrdensPorCompraPassada)
FROM (
SELECT CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)) AS `mesCompra`,(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`))) AS `quantidadeComprasPassadas`, COUNT(0) AS `quantidadeOrdensPorCompraPassada`
FROM `sales_flat_order` `sfo`
WHERE (`sfo`.`customer_id` IS NOT NULL AND `sfo`.`customer_email` not like ' . $_testUsers . ') and `sfo`.status != "canceled"
GROUP BY CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)),(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`)))) vrod2
WHERE vrod2.mesCompra = vrod.mesCompra AND vrod2.quantidadeComprasPassadas = 1))/ SUM(vrod.quantidadeOrdensPorCompraPassada), 2) AS col2,
round((100*(
SELECT SUM(vrod2.quantidadeOrdensPorCompraPassada)
FROM (
SELECT CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)) AS `mesCompra`,(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`))) AS `quantidadeComprasPassadas`, COUNT(0) AS `quantidadeOrdensPorCompraPassada`
FROM `sales_flat_order` `sfo`
WHERE (`sfo`.`customer_id` IS NOT NULL AND `sfo`.`customer_email` not like ' . $_testUsers . ') and `sfo`.status != "canceled"
GROUP BY CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)),(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`)))) vrod2
WHERE vrod2.mesCompra = vrod.mesCompra AND vrod2.quantidadeComprasPassadas = 2))/ SUM(vrod.quantidadeOrdensPorCompraPassada), 2) AS col3,
round((100*(
SELECT SUM(vrod2.quantidadeOrdensPorCompraPassada)
FROM (
SELECT CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)) AS `mesCompra`,(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`))) AS `quantidadeComprasPassadas`, COUNT(0) AS `quantidadeOrdensPorCompraPassada`
FROM `sales_flat_order` `sfo`
WHERE (`sfo`.`customer_id` IS NOT NULL AND `sfo`.`customer_email` not like ' . $_testUsers . ') and `sfo`.status != "canceled"
GROUP BY CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)),(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`)))) vrod2
WHERE vrod2.mesCompra = vrod.mesCompra AND vrod2.quantidadeComprasPassadas >= 3))/ SUM(vrod.quantidadeOrdensPorCompraPassada), 2) AS col4
FROM (
SELECT CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)) AS `mesCompra`,(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`))) AS `quantidadeComprasPassadas`, COUNT(0) AS `quantidadeOrdensPorCompraPassada`
FROM `sales_flat_order` `sfo`
WHERE (`sfo`.`customer_id` IS NOT NULL AND `sfo`.`customer_email` not like ' . $_testUsers . ') and `sfo`.status != "canceled"
GROUP BY CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)),(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`)))) vrod
GROUP BY mesCompra';

	$col = null;
	$pattern = '(`sfo`.`customer_id` IS NOT NULL';
	$this->setCurrentWebsiteFilter($col, $query, $pattern);

	$resource = Mage::getSingleton('core/resource');
	$readConnection = $resource->getConnection('core_read');
	$results = $readConnection->fetchAll($query);
	//echo $query;

	$this->setCollection($results);
	return $results;
  }

  public function getRebuyFirstTimeSales()
  {

	//formatando a lista de emails teste para removermos da query;
	$_testUsers = $this->testUsers[0];

	$query = 'SELECT vrod.mesCompra as label,
round((100*(
SELECT SUM(vrod2.quantidadeOrdensPorCompraPassada)
FROM (
SELECT CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)) AS `mesCompra`,(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`))) AS `quantidadeComprasPassadas`, COUNT(0) AS `quantidadeOrdensPorCompraPassada`
FROM `sales_flat_order` `sfo`
WHERE (`sfo`.`customer_id` IS NOT NULL AND `sfo`.`customer_email` not like \'' . $_testUsers . '\')
GROUP BY CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)),(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`)))) vrod2
WHERE vrod2.mesCompra = vrod.mesCompra AND vrod2.quantidadeComprasPassadas = 0))/ SUM(vrod.quantidadeOrdensPorCompraPassada), 2) AS value
FROM (
SELECT CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)) AS `mesCompra`,(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`))) AS `quantidadeComprasPassadas`, COUNT(0) AS `quantidadeOrdensPorCompraPassada`
FROM `sales_flat_order` `sfo`
WHERE (`sfo`.`customer_id` IS NOT NULL AND `sfo`.`customer_email` not like \'' . $_testUsers . '\')
GROUP BY CONCAT(YEAR(`sfo`.`created_at`),"/", MONTH(`sfo`.`created_at`)),(
SELECT COUNT(0)
FROM `sales_flat_order` `sfo2`
WHERE ((`sfo2`.`customer_id` = `sfo`.`customer_id`) AND (`sfo2`.`created_at` < `sfo`.`created_at`)))) vrod
GROUP BY mesCompra';

	$col = null;
	//o valor do $pattern serve para o metodo saber onde que ele deve inserir o código SQL de validação
	//por exemplo, o método "setCurrentWebsiteFilter" vai procurar na variável $query esse pattern e inserir o código de filtro de website logo após;
	$pattern = '(`sfo`.`customer_id` IS NOT NULL';
	$this->setCurrentWebsiteFilter($col, $query, $pattern);

	$resource = Mage::getSingleton('core/resource');
	$readConnection = $resource->getConnection('core_read');
	$results = $readConnection->fetchAll($query);
	//echo $query;

	$this->setCollection($results);
	return $results;
  }

  public function getCustomersBought()
  {
	$_websiteFilter = $this->getWebsiteFilter();

	$websiteCondition = "= $_websiteFilter";

	if (isset($_websiteFilter) && $_websiteFilter == 0)
	{
	  $websiteCondition = "not in (-1)";
	}

	$query = "SELECT '% Base Compradora' as label, COUNT(DISTINCT(ce.entity_id))*100 / (
					SELECT COUNT(*)
					FROM customer_entity ce2
					WHERE ce2.website_id $websiteCondition and ce2.mp_cc_is_approved = " . MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED . ") as value
				FROM customer_entity ce
				JOIN sales_flat_order sfo ON sfo.customer_id = ce.entity_id
				WHERE ce.website_id $websiteCondition AND sfo.`status` not in ('$this->badOrderStatus') AND ce.email NOT LIKE '$this->testUsersStr' and ce.mp_cc_is_approved = " . MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED;

	$resource = Mage::getSingleton('core/resource');
	$readConnection = $resource->getConnection('core_read');
	$results = $readConnection->fetchAll($query);
	//echo $query;

	$this->setCollection($results);
	return $results;
  }

  private function getChildWebsitesFromBrandWebsite($website)
  {
	$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('code', array('like' => '%_' . $website->getCode()));

	$websitesArray = array();
	foreach ($stores as $store)
	{
	  $websitesArray[] = $store->getWebsite();
	}

	return $websitesArray;
  }

  private function getChildStoresFromBrandWebsite($website)
  {
	return Mage::getModel('core/store')->getCollection()->addFieldToFilter('code', array('like' => '%_' . $website->getCode()));
  }

  private function getCategoryPathFilterStringByWebsite($website)
  {
	if ($website->getConfig('general/store_information/is_brand') == 1)
	{
	  $websitesArray = $this->getChildWebsitesFromBrandWebsite($website);
	  $pathFilter = ' and (';
	  foreach ($websitesArray as $website)
	  {
		$pathFilter = $pathFilter . 'path like "1/' . $website->getGroupCollection()->getFirstItem()->getRootCategoryId() . '/%" or ';
	  }
	  $pathFilter = chop($pathFilter, ' or ');
	  $pathFilter = $pathFilter . ')';
	} else
	{
	  $rootCategoryId = $website->getGroupCollection()->getFirstItem()->getRootCategoryId();
	  $pathFilter = ' and path like "1/' . $rootCategoryId . '/%"';
	}

	return $pathFilter;
  }
}
