<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/21/15
 * Time: 3:59 PM
 */

$local = false;

$path = '/wwwroot/current/shell/scripts/datavalidate/';

if ($local == true)
{
	$requiredFile = './config.php';
} else
{
	$requiredFile = $path . 'config.php';
}

require_once $requiredFile;

$customerDeniedEmails = array('%@4vets.com.br');

$channel = 'datavalidate';
$filename = date('Ymd') . '-relatorioOrders.csv';
$filePath = Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'datavalidate' . DS . 'extras' . DS . $filename;

$fromDate = (date("Y-m-d 00:00:01", strtotime("-365 days")));
$toDate = (date("Y-m-d 23:59:59"));

$header = "Número do Pedido|Data da Realização|Grupo de Acesso|ID 4VETS do Cliente| ID ERP do Cliente|Nome do Cliente|Razão Social do Cliente|Nome do Representante do Cliente|Valor Total da Venda|Desconto total";
$finalResult = "";
$finalResult .= ($header . "\n");

$totalOrders = 0;

foreach ($websitesId as $websiteId)
{
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
		$customerId = $order->getCustomerId();

		$customer = Mage::getModel('customer/customer')->load($customerId);

		$storeViewName = Mage::getModel('core/store')->load($order->getStoreId())->getName();
		$salesrepId = $customer->getFvetsSalesrep();
		$salesrep = Mage::getModel('fvets_salesrep/salesrep')->load($salesrepId);
		$finalResult .= $order->getIncrementId() . "|" . $order->getCreatedAt() .  "|" . $storeViewName . "|" . $customer->getId() . "|" . $customer->getIdErp() . "|" . $customer->getName() . "|" . $customer->getRazaoSocial() . "|" . $salesrep->getName() .  "|" . $order->getBaseSubtotal() . "|" . $order->getDiscountAmount() . "\n";

	}
	$totalOrders += count($dailyOrders);
}

if (file_put_contents($path . 'extras' . DS . $filename, $finalResult))
{
	//Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), $totalOrders . " Vendas foram realizadas no dia de ontem", $filePath, 'csv', $channel);
} else
{
	$errorMsg = "Arquivo " . $filename . " não salvo";
	echo $errorMsg . "\n";
	//Mage::helper('datavalidate')->sendSlackMessage($channel, $errorMsg);
}

if (file_exists($filePath))
{
	//unlink($filePath);
} else
{
	// code when file not found
}

function getProductCategoryName($product)
{
	$categoryIds = $product->getCategoryIds();

	foreach ($categoryIds as $categoryId)
	{
		$_category = Mage::getModel('catalog/category')->load($categoryId);
		if ($_category->getLevel() == 2)
		{
			return $_category->getName();
		}
	}
}