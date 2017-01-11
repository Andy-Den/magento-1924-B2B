<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/21/15
 * Time: 3:59 PM
 */

//ini_set('memory_limit', '2048M');

$local = false;

$path = '/wwwroot/whitelabel/current/shell/scripts/datavalidate/';

if ($local == true)
{
	$requiredFile = './config.php';
} else
{
	$requiredFile = $path. DIRECTORY_SEPARATOR . 'config.php';
}

require_once $requiredFile;

$customerDeniedEmails = array('%@4vets.com.br%');

$channel = 'datavalidate';
$filename = date('Ymd') . '-relatorioOrders.csv';
$filePath = Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'datavalidate' . DS . 'extras' . DS . $filename;

$fromDate = (date("Y-m-d 00:00:01", strtotime("-10000 days")));

//$fromDate = (date("Y-m-d 00:00:01", strtotime("-30 days")));

$toDate = new DateTime("-1 days", new DateTimeZone('America/Sao_Paulo'));
$toDate->setTime(23, 59, 59);
$toDate->setTimezone(new DateTimeZone('UTC'));
$toDate = $toDate->format('Y-m-d H:i:s');

$header = "Número do Pedido|Data|Grupo de Acesso|ID 4VETS do Cliente| ID ERP do Cliente|Nome do Cliente|Razão Social do Cliente|Código do representante|Nome do Representante|Código 4VETS do Produto|Código ERP do Produto|Nome do Produto|Categoria(Marca)|Quantidade de Itens|Preço por Item|Preço Total|Desconto Total|Origem|Cidade";
$finalResult = "";
$finalResult .= ($header . "\n");

$totalOrders = 0;

$customer = array();
$salesrep = array();
$storeview = array();
$product = array();
$category = array();

$lastStoreId = 0;

foreach ($websitesId as $websiteId)
{
	echo 'Site: ' . $websiteId . "\n";

	$storesid = getStoreidsByWebsiteId($websiteId);

	$dailyOrders = Mage::getModel('sales/order')->getCollection()
		->addFieldtoFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate))
		->addFieldtoFilter('status', array('neq' => 'canceled'))
		->addFieldtoFilter('store_id', array('in' => $storesid));

	foreach ($customerDeniedEmails as $customerDeniedEmail)
	{
		$dailyOrders->addFieldToFilter('customer_email', array('nlike' => $customerDeniedEmail));
	}

	foreach ($dailyOrders as $order)
	{
		echo 'o';

		if ($lastStoreId != $order->getStoreId())
		{
			Mage::app()->setCurrentStore($order->getStoreId());
			$lastStoreId = $order->getStoreId();
		}

		$customerId = $order->getCustomerId();

		//Retrieve Customer
		if (!key_exists($customerId, $customer))
		{
			$customer[$customerId] = Mage::getModel('customer/customer')->load($customerId);
		}

		//Retrieve Storeview
		if (!key_exists($order->getStoreId(), $storeview))
		{
			$storeview[$order->getStoreId()] = Mage::getModel('core/store')->load($order->getStoreId());
		}
		$storeViewName = $storeview[$order->getStoreId()]->getName();

		foreach ($order->getAllVisibleItems() as $item)
		{
			echo 'i';

			//Retrieve Product
			if (!key_exists($item->getProductId() . $order->getStoreId(), $product))
			{
				$product[$item->getProductId() . $order->getStoreId()] = Mage::getModel('catalog/product')->load($item->getProductId());

				//Alguns produtos foram trocados no banco de dados, consequentemente os IDS estão errados.
				//Caso não encontre o produto listado no pedido, procura pelo SKU
				if ($product[$item->getProductId() . $order->getStoreId()]->getName() == '')
				{
					$product[$item->getProductId() . $order->getStoreId()] =  Mage::getModel('catalog/product')
						->getCollection()
						->addAttributeToSelect('name')
						->addAttributeToSelect('id_erp')
						->addAttributeToFilter('sku', $item->getSku())
						->getFirstItem();
				}
			}
			$idErpProduct = $product[$item->getProductId() . $order->getStoreId()]->getIdErp();
			$productName = $product[$item->getProductId() . $order->getStoreId()]->getName();
			$productSku =  $product[$item->getProductId() . $order->getStoreId()]->getSku();

			//Retrieve Salesrep
			if (!key_exists($item->getSalesrepId(), $salesrep))
			{
				$salesrep[$item->getSalesrepId()] = Mage::getModel('fvets_salesrep/salesrep')->load($item->getSalesrepId());
			}

			$productCategoryName = getProductCategoryName($product[$item->getProductId() . $order->getStoreId()]);

			//convertendo para America/Sao_Paulo para exibir no relatório
			$createdAt = new DateTime($order->getCreatedAt());
			$createdAt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
			$createdAt = $createdAt->format('Y-m-d H:i:s');

			//adicionando cidade
			$defaultBillingId = $customer[$customerId]->getDefaultBilling();

			$cidade = '';
			if($defaultBillingId) {
				$address = Mage::getModel('customer/address')->load($defaultBillingId);
				if($address) {
					$cidade = $address->getCity() ? $address->getCity() : '';
				}
			}

			$finalResult .= $order->getIncrementId() . "|" . $createdAt . "|" . $storeViewName . "|" . $customer[$customerId]->getId() . "|" . $customer[$customerId]->getIdErp() . "|" . $customer[$customerId]->getName() . "|" . $customer[$customerId]->getRazaoSocial() . "|" . $salesrep[$item->getSalesrepId()]->getIdErp() . "|" . $salesrep[$item->getSalesrepId()]->getName() . "|" . $productSku . "|" . $idErpProduct . "|" . $productName . "|" . $productCategoryName . "|" . $item->getQtyOrdered() . "|" . $item->getOriginalPrice() . "|" . $item->getRowTotal() . "|" . $item->getDiscountAmount() . "|" . $customer[$customerId]->getOrigem() .  "|" . formatString($cidade) . "\n";
		}
	}
	$totalOrders += count($dailyOrders);

	echo "\n";
	echo convert(memory_get_usage(true)); // 123 kb
	echo "\n";
}

echo "\n";

if (file_put_contents($path. DS . 'extras' . DS . $filename, $finalResult))
{
	Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), $totalOrders . " - Total de vendas", $filePath, 'csv', $channel);
} else
{
	$errorMsg = "Arquivo " . $filename . " não salvo";
	echo $errorMsg . "\n";
	Mage::helper('datavalidate')->sendSlackMessage($channel, $errorMsg);
}

if (file_exists($filePath))
{
	unlink($filePath);
} else
{
	// code when file not found
}

echo "Bye";

function getProductCategoryName($product)
{
	global $category;
	$categoryIds = $product->getCategoryIds();
	$root = Mage::app()->getStore()->getRootCategoryId();

	foreach ($categoryIds as $categoryId)
	{
		//Retrieve Categories
		if (!key_exists($categoryId, $category))
		{
			$category[$categoryId] = Mage::getModel('catalog/category')->load($categoryId);
		}

		if ($category[$categoryId]->getLevel() == 2 && ($category[$categoryId]->getParentId() == $root || $categoryId == $root))
		{
			return $category[$categoryId]->getName();
		}
	}
}

function convert($size)
{
	$unit=array('b','kb','mb','gb','tb','pb');
	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

function formatString($str) {
	$str = preg_replace('/[áàãâä]/ui', 'a', $str);
	$str = preg_replace('/[éèêë]/ui', 'e', $str);
	$str = preg_replace('/[íìîï]/ui', 'i', $str);
	$str = preg_replace('/[óòõôö]/ui', 'o', $str);
	$str = preg_replace('/[úùûü]/ui', 'u', $str);
	$str = preg_replace('/[ç]/ui', 'c', $str);
	// $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
	$str = preg_replace('/[^a-z0-9]/i', '_', $str);
	$str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
	return strtoupper($str);
}
