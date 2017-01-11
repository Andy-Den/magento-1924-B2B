<?php

require_once '/wwwroot/whtielabel/current/shell/services/configService.php';
require_once '/wwwroot/whitelabel/current/shell/services/util/util.php';

array_shift($argv);
list($websiteId, $fromDate, $toDate, $publicPath) = $argv;

if (empty($websiteId) || empty($fromDate) || empty($toDate) || empty($publicPath)) {
	die('Você deve informar todos os parâmetros.');
}

$websites = array();
if (isset($websiteId) && $websiteId != '-1') {
	$websites[] = Mage::getModel('core/website')->load($websiteId);
} else {
	$websites = Mage::app()->getWebsites();
}

//$websites = array(Mage::getModel('core/website')->load(2));
//$publicPath = "/var/www/html/integra-web/public/reports/orders/";
//$publicPath = "/tmp/magento_services/";

$customerDeniedEmails = array('%@4vets.com.br%');

$filename = 'orders.xls';
$filePath = $publicPath . $filename;
$data = array();

//$fromDate = '2016-01-01';
$fromDate = (date("Y-m-d 00:00:01", strtotime($fromDate)));
$fromDate = new DateTime($fromDate, new DateTimeZone('America/Sao_Paulo'));
$fromDate->setTime(00, 00, 01);
$fromDate->setTimezone(new DateTimeZone('UTC'));
$fromDate = $fromDate->format('Y-m-d H:i:s');

//$toDate = '2016-01-06';
$toDate = (date("Y-m-d 23:59:59", strtotime($toDate)));
$toDate = new DateTime($toDate, new DateTimeZone('America/Sao_Paulo'));
$toDate->setTime(23, 59, 59);
$toDate->setTimezone(new DateTimeZone('UTC'));
$toDate = $toDate->format('Y-m-d H:i:s');

//$header = "Número do Pedido|Data|Grupo de Acesso|ID 4VETS do Cliente| ID ERP do Cliente|Nome do Cliente|Razão Social do Cliente|Código do representante|Nome do Representante|Código 4VETS do Produto|Código ERP do Produto|Nome do Produto|Categoria(Marca)|Quantidade de Itens|Preço por Item|Preço Total|Desconto Total|Origem";
$header = array('Número do Pedido','Data','Grupo de Acesso','ID 4VETS do Cliente', 'ID ERP do Cliente','Nome do Cliente','Razão Social do Cliente','Código do representante','Nome do Representante', 'Código 4VETS do Produto', 'Código ERP do Produto', 'Nome do Produto', 'Categoria(Marca)', 'Quantidade de Itens', 'Preço por Item', 'Preço Total', 'Desconto Total', 'Origem');
$data[0] = $header;

$finalResult = "";
$finalResult .= ($header . "\n");

$totalOrders = 0;

$customer = array();
$salesrep = array();
$storeview = array();
$product = array();
$category = array();

$lastStoreId = 0;

$count = 1;
foreach ($websites as $website) {
	$storesid = getStoreidsByWebsiteId($website->getId());

	$dailyOrders = Mage::getModel('sales/order')->getCollection()
		->addFieldtoFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate))
		->addFieldtoFilter('status', array('neq' => 'canceled'))
		->addFieldtoFilter('store_id', array('in' => $storesid));

	foreach ($customerDeniedEmails as $customerDeniedEmail) {
		$dailyOrders->addFieldToFilter('customer_email', array('nlike' => $customerDeniedEmail));
	}

	foreach ($dailyOrders as $order) {

		if ($lastStoreId != $order->getStoreId()) {
			Mage::app()->setCurrentStore($order->getStoreId());
			$lastStoreId = $order->getStoreId();
		}

		$customerId = $order->getCustomerId();

		//Retrieve Customer
		if (!key_exists($customerId, $customer)) {
			$customer[$customerId] = Mage::getModel('customer/customer')->load($customerId);
		}

		//Retrieve Storeview
		if (!key_exists($order->getStoreId(), $storeview)) {
			$storeview[$order->getStoreId()] = Mage::getModel('core/store')->load($order->getStoreId());
		}
		$storeViewName = $storeview[$order->getStoreId()]->getName();

		foreach ($order->getAllVisibleItems() as $item) {

			//Retrieve Product
			if (!key_exists($item->getProductId() . $order->getStoreId(), $product)) {
				$product[$item->getProductId() . $order->getStoreId()] = Mage::getModel('catalog/product')->load($item->getProductId());

				//Alguns produtos foram trocados no banco de dados, consequentemente os IDS estão errados.
				//Caso não encontre o produto listado no pedido, procura pelo SKU
				if ($product[$item->getProductId() . $order->getStoreId()]->getName() == '') {
					$product[$item->getProductId() . $order->getStoreId()] = Mage::getModel('catalog/product')
						->getCollection()
						->addAttributeToSelect('name')
						->addAttributeToSelect('id_erp')
						->addAttributeToFilter('sku', $item->getSku())
						->getFirstItem();
				}
			}
			$idErpProduct = $product[$item->getProductId() . $order->getStoreId()]->getIdErp();
			$productName = $product[$item->getProductId() . $order->getStoreId()]->getName();
			$productSku = $product[$item->getProductId() . $order->getStoreId()]->getSku();

			//Retrieve Salesrep
			if (!key_exists($item->getSalesrepId(), $salesrep)) {
				$salesrep[$item->getSalesrepId()] = Mage::getModel('fvets_salesrep/salesrep')->load($item->getSalesrepId());
			}

			$productCategoryName = getProductCategoryName($product[$item->getProductId() . $order->getStoreId()]);

			//convertendo para America/Sao_Paulo para exibir no relatório
			$createdAt = new DateTime($order->getCreatedAt());
			$createdAt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
			$createdAt = $createdAt->format('Y-m-d H:i:s');

			//$finalResult .= $order->getIncrementId() . "|" . $createdAt . "|" . $storeViewName . "|" . $customer[$customerId]->getId() . "|" . $customer[$customerId]->getIdErp() . "|" . $customer[$customerId]->getName() . "|" . $customer[$customerId]->getRazaoSocial() . "|" . $salesrep[$item->getSalesrepId()]->getIdErp() . "|" . $salesrep[$item->getSalesrepId()]->getName() . "|" . $productSku . "|" . $idErpProduct . "|" . $productName . "|" . $productCategoryName . "|" . $item->getQtyOrdered() . "|" . $item->getOriginalPrice() . "|" . $item->getRowTotal() . "|" . $item->getDiscountAmount() . "|" . $customer[$customerId]->getOrigem() . "\n";
			$data[$count] = array(formatString($order->getIncrementId()), $createdAt, formatString($storeViewName), formatString($customer[$customerId]->getId()), formatString($customer[$customerId]->getIdErp()), formatString($customer[$customerId]->getName()), formatString($customer[$customerId]->getRazaoSocial()), formatString($salesrep[$item->getSalesrepId()]->getIdErp()), formatString($salesrep[$item->getSalesrepId()]->getName()), formatString($productSku), formatString($idErpProduct), formatString($productName), formatString($productCategoryName), formatString($item->getQtyOrdered()), formatString($item->getOriginalPrice()), formatString($item->getRowTotal()), formatString($item->getDiscountAmount()), formatString($customer[$customerId]->getOrigem()));
			$count++;
		}
	}
	$totalOrders += count($dailyOrders);
}

// Unparsing in Excel Format
$xmlObj = new Varien_Convert_Parser_Xml_Excel();
$xmlObj->setVar('single_sheet', 'Orders');
$xmlObj->setData($data);
$xmlObj->unparse();
$content=$xmlObj->getData();

$return = file_put_contents($filePath, $content);
if ($return) {
	echo 'ok';
	//Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), $totalOrders . " - Total de vendas", $filePath, 'csv', $channel);
} else {
	die($return);
	$errorMsg = "Arquivo " . $filename . " não salvo";
	echo $errorMsg . PHP_EOL;
	//Mage::helper('datavalidate')->sendSlackMessage($channel, $errorMsg);
}

function getProductCategoryName($product)
{
	global $category;
	$categoryIds = $product->getCategoryIds();
	$root = Mage::app()->getStore()->getRootCategoryId();

	foreach ($categoryIds as $categoryId) {
		//Retrieve Categories
		if (!key_exists($categoryId, $category)) {
			$category[$categoryId] = Mage::getModel('catalog/category')->load($categoryId);
		}

		if ($category[$categoryId]->getLevel() == 2 && ($category[$categoryId]->getParentId() == $root || $categoryId == $root)) {
			return $category[$categoryId]->getName();
		}
	}
}

function formatString($data) {
	return preg_replace("/[^a-zA-Z0-9\s]/", '', strip_tags($data));
}
