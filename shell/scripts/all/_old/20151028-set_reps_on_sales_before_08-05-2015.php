<?php
require_once './configScript.php';
require_once './helper.php';

$fromDate = (date("Y-m-d 00:00:01", strtotime("-10000 days")));
$toDate = date("2015-05-08");
$orders = Mage::getModel('sales/order')->getCollection()
	->addFieldtoFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate));

$salesrepHelper = Mage::helper('fvets_salesrep');

foreach ($orders as $order)
{
	Mage::app()->setCurrentStore($order->getStoreId());
	//echo $order->getCreatedAt() . "\n";
	$items = $order->getAllItems();
	foreach ($items as $item)
	{
		if (!$item->getSalesrepId())
		{
			if (!$order->getCustomerId())
			{
				continue;
			}
			if (strpos($order->getCustomerEmail(), '@4vets.com.br') != false)
			{
				continue;
			}
			$oldSalesrep = getCustomerSalesrepFromFile($order->getCustomerId(), Mage::getModel('core/store')->load($order->getStoreId())->getWebsiteId());
			$salesRep = $salesrepHelper->getSalesrepByCustomerAndProduct(Mage::getModel('customer/customer')->load($order->getCustomerId()), Mage::getModel('catalog/product')->load($item->getProductId()));

			echo($order->getCustomerId() . ' => ' . ($oldSalesrep ? $oldSalesrep : '') . ' => ' . ($salesRep->getId() ? $salesRep->getId() : '')) . "\n";
			if (!$oldSalesrep && !$salesRep->getId()) {
				continue;
			}

			if (!$oldSalesrep && $salesRep->getId()) {
				$item->setSalesrepId($salesRep->getId());
				continue;
			}

			if (!Mage::getModel('fvets_salesrep/salesrep')->load($oldSalesrep)->getId()) {
				continue;
			} else {
				$item->setSalesrepId($oldSalesrep);
			}
		}
	}
	$order->save();
}

function getCustomerSalesrepFromFile($customerId, $websiteId)
{
	$file = file("./extras/2015-10-28_customersXreps_" . $websiteId . ".csv", FILE_IGNORE_NEW_LINES);
	foreach ($file as $key => $value)
	{
		if (explode('|', $value)[0] == $customerId)
		{
			return explode('|', $value)[1];
			break;
		}
	}
	return null;
}

function saveItem($itemId, $salesrepId) {

}