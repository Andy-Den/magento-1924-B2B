<?php

$customersByErp = array();
$productsByErp = array();
$coreWrite = null;
$orderItems = array();

function debug($message)
{
	global $debug;

	if ($debug)
		echo $message . "\n";
}

function getCoreWrite()
{
	global $coreWrite;

	if (!isset($coreWrite))
	{
		$coreWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
	}

	return $coreWrite;
}

function getCustomerByIdErp($idErp)
{
	global $websiteId, $customersByErp;

	if (!$customer = $customersByErp[$idErp]) {
		$customer = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToSelect('id_erp')
			->addAttributeToFilter('website_id', $websiteId)
			->addAttributeToFilter('id_erp', $idErp)
			->setPageSize(1)
			->setCurPage(1);

		foreach($customer as $customer)
		{
			$customersByErp[$idErp] = $customer;
		}
	}

	return $customersByErp[$idErp];
}

function getOrderById($id)
{
	$order = Mage::getModel('sales/order')->load($id);
	return $order;
}

function getOrderByIncrementId($incrementId)
{
	$order = Mage::getModel('sales/order')->load($incrementId, 'increment_id');
	return $order;

	/*$write = getCoreWrite();
	$result=$write->query('SELECT entity_id as id, store_id FROM `sales_flat_order` WHERE `increment_id` = "'.$incrementId.'" ');
	$row = $result->fetch();
	$object = new Varien_Object();
	$object->setData($row);
	return $object;*/
}

function getQuoteById($quote_id)
{
	$quote = Mage::getModel('sales/quote')->load($quote_id);
	return $quote;
}

function getQuoteItemById($quote_item_id)
{
	$quote_item = Mage::getModel('sales/quote_item')->load($quote_item_id);
	return $quote_item;
}

function getItemsQty($orderItems) {
	$itemsQty = array();
	foreach ($orderItems as $item) {
		$itemsQty[$item->getId()] = $item->getQtyOrdered() - $item->getQtyCanceled();
	}
	return $itemsQty;
}

function getAllItemsQty($orderItems, $order)
{
	$itemsQty = getItemsQty($orderItems);
	$items = $order->getAllItems();
	//Itens que não estão na order, colocar zero.
	foreach($items as $item)
	{
		if (!isset($itemsQty[$item->getId()]))
		{
			$itemsQty[$item->getId()] = 0;
		}
	}
	return $itemsQty;
}

/**
 * @deprecated Use: getAllItemsQty($orderItems, $order)
 */
function getItemsQtyBySalesrep($orderItems, $order, $addAllOrderProducts = true)
{
	return getAllItemsQty($orderItems, $order);
}

function getProductByIdErp($idErp)
{

	global $websiteId, $productsByErp;

	if (!isset($productsByErp[$idErp])) {
		$product = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect(array('sku','name', 'weight', 'description', 'price'))
			->addAttributeToFilter('id_erp', $idErp)
			->addAttributeToFilter('id_erp', $idErp)
			->load();

		foreach ($product as $product) {
			$productsByErp[$idErp] = $product;
			break;
		}
	}

	return $productsByErp[$idErp];


	/*$write = getCoreWrite();
	$result=$write->query("SELECT main_table.*
		FROM `catalog_product_entity_varchar` as main_table
		WHERE main_table.`attribute_id` = 185
		AND main_table.`store_id` = ".$order->getStoreId()."
		AND main_table.`value` = ".$idErp.";");
	$row = $result->fetch();
	return $row['entity_id'];*/



}

function getProductIdByIdErp($order, $idErp)
{

	$write = getCoreWrite();
	$result=$write->query("SELECT main_table.entity_id
		FROM `catalog_product_entity_varchar` as main_table
		WHERE main_table.`attribute_id` = 185
		AND main_table.`store_id` = ".$order->getStoreId()."
		AND main_table.`value` = ".$idErp.";");
	$row = $result->fetch();

	if (!$row['entity_id'])
	{
		mage::throwException('Produto nao encontrado. Id ERP:'.$row['entity_id'].' | store_id:'.$order->getStoreId());
	}

	return $row['entity_id'];
}

function billOrder($order)
{
	$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $this->__('Pedido faturado'), true)->save();
}

function cancelOrder($order, $fileItems, $salesrep = NULL)
{

	//Cancela todos os itens do pedido
	foreach ($fileItems as $key => $value)
	{
		$fileItems[$key][item_line_qty] = 0;
	}
	editOrderItems($order, $fileItems);

	//Verifica se todos os itens foram cancelados para poder cancelar a order.
	$items  = $order->getAllItems();

	$cancelOrder = true;
	foreach($items as $item)
	{
		if ((bool)($item->getQtyOrdered() - $item->getQtyCanceled()))
		{
			$cancelOrder = false;
			break;
		}
	}


	try {

		if ($cancelOrder) {
			$order->cancel();
		}

		/*/ remove status history set in _setState
		$order->getStatusHistoryCollection(true);

		// do some more stuff here
		// ...*/

		$order->save();
		debug('Pedido '.$order->getId().' cancelado.');
	} catch (Exception $e) {
		Mage::logException($e);
		mage::throwException(Mage::helper('core')->__('Nao foi possivel cancelar a ordem: %s', $e->getMessage()));
	}
}

function invoiceOrder($order, $itemsQty)
{

	if($order->canInvoice()) {

		try {
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($itemsQty);
			// The rest is only required when handling a partial invoice
			$amount = $invoice->getGrandTotal();
			$invoice->register()->pay();
			$invoice->getOrder()->setIsInProcess(true);

			$history = $invoice->getOrder()->addStatusHistoryComment(
				'Pagamento de ' . $amount . ' capturado.', false
			);

			$invoice->getOrder()->setIsInProcess(true);

			$history->setIsCustomerNotified(false);

			try
			{
				$order->save();

				Mage::getModel('core/resource_transaction')
					->addObject($invoice)
					->addObject($invoice->getOrder())
					->save();
				debug('Pedido '.$order->getId().' pago: ' . $amount);
			}
			catch (Exception $e)
			{
				debug('Pedido não pago: ' . $e->getMessage());
			}

		}
		catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			mage::throwException(Mage::helper('core')->__('Nao foi possivel efetuar o pagamento: %s', $e->getMessage()));
		}

	} else {
		echo 'O pedido não pode ser pago'."\n";
	}

}

function shipOrder($order, $itemsQty)
{
	//$shipment = $order->getShipmentsCollection()->getFirstItem();

	//if (!$shipment->getData('entity_id')) {

		$shipment = Mage::getModel('sales/service_order', $order);
		$shipment->prepareShipment($itemsQty);
		$shipment->getOrder()->setIsInProcess(true);
		$transactionSave = Mage::getModel('core/resource_transaction');
		$transactionSave->addObject($shipment);
		$transactionSave->addObject($shipment->getOrder());
		$transactionSave->save();

		//$order->addStatusHistoryComment('Pedido finalizado pela distribuidora.');
		$order->save();
	/*} else {
		echo 'O pedido já foi enviado'."\n";
	}*/
}

function completeOrder($order)
{
	//$order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, true, Mage::helper('sales')->__('Pedido completo'), false)->save();
	//$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_COMPLETE);

	if ($order->getState() != Mage_Sales_Model_Order::STATE_COMPLETE)
	{
		if(abs($order->getGrandTotal() - $order->getBaseTotalPaid()) < 0.00001) {
			$order->setData('state', Mage_Sales_Model_Order::STATE_COMPLETE);
			$order->setStatus("complete");

			$order->addStatusHistoryComment('Pedido finalizado pela distribuidora.');

			try
			{
				$order->save();
				debug('Pedido '.$order->getId().' finalizado.');
			}
			catch (Exception $e)
			{
				debug('Pedido não finalizado: ' . $e->getMessage());
			}
		}
	} else {
		echo 'O pedido já foi finalizado'."\n";
	}
}

/**
 * @param Mage_Sales_Model_Order $order
 * @param FVets_Salesrep_Model_Salesrep $salesrep
 * @param string $tableprice
 * @return int
 */
function getTotalQtyOrdered(Mage_Sales_Model_Order $order, FVets_Salesrep_Model_Salesrep $salesrep = null, string $tableprice = null)
{
	$items = Mage::getResourceModel('sales/order_item_collection')
		->addAttributeToFilter('order_id', $order->getId());

	if ($salesrep && isset($salesrep))
	{
		$items->addFieldToFilter('salesrep_id', $salesrep);
	}

	if ($tableprice && isset($tableprice))
	{
		$items->addFieldToFilter('tableprice', $tableprice);
	}

	$count = 0;
	foreach ($items as $item) {
		$count += $item->getQtyOrdered();
	}

	return $count;
}

/**
 * @deprecated Use: getTotalItemCount(Mage_Sales_Model_Order $order, FVets_Salesrep_Model_Salesrep $salesrep = null, string $tableprice = null)
 */
function getTotalQtyOrderedBySalesrep($order, $salesrep)
{
	return getTotalQtyOrdered($order, $salesrep);
}

/**
 * @param Mage_Sales_Model_Order $order
 * @param FVets_Salesrep_Model_Salesrep $salesrep
 * @param string $tableprice
 * @return int
 */
function getTotalItemCount(Mage_Sales_Model_Order $order, FVets_Salesrep_Model_Salesrep $salesrep = null, string $tableprice = null)
{
	$items = Mage::getResourceModel('sales/order_item_collection')
		->addAttributeToFilter('order_id', $order->getId());

	if ($salesrep && isset($salesrep))
	{
		$items->addFieldToFilter('salesrep_id', $salesrep);
	}

	if ($tableprice && isset($tableprice))
	{
		$items->addFieldToFilter('tableprice', $tableprice);
	}

	return $items->count();
}

/**
 * @deprecated Use: getTotalItemCount(Mage_Sales_Model_Order $order, FVets_Salesrep_Model_Salesrep $salesrep = null, string $tableprice = null)
 */
function getTotalItemCountBySalesrep($order, $salesrep)
{
	return getTotalItemCount($order, $salesrep);
}

function getOrderItemsBySalesrep($order, $salesrep)
{
	$items = Mage::getResourceModel('sales/order_item_collection')
		->addAttributeToFilter('order_id', $order->getId())
		->addFieldToFilter('salesrep_id', $salesrep)
	;

	return $items;
}

function updateOrderPrices($order)
{
	$write = getCoreWrite();

	$orderSubtotal = 0;
	$orderDiscountAmount = 0;
	$orderGrandTotal = 0;
	$orderItems = 0;
	$orderWeight = 0;
	$totalItems=0;

	foreach($order->getAllItems() as $item)
	{
		$totalItems++;
		$orderSubtotal += $item->getRowTotal();
		$orderDiscountAmount += $item->getDiscountAmount();
		$orderGrandTotal += $item->getRowTotal() - $item->getDiscountAmount();
		$orderItems += $item->getQtyOrdered() - $item->getQtyCanceled();
		$orderWeight += $item->getRowWeight();
	}

	//$orderData = $order->getData();
	$orderData['base_subtotal'] = //Total sem desconto
	$orderData['base_subtotal_incl_tax'] =
	$orderData['subtotal'] =
	$orderData['subtotal_incl_tax'] = $orderSubtotal;
	$orderData['base_discount_amount'] =  //Total do desconto somado
	$orderData['discount_amount'] = $orderDiscountAmount;
	$orderData['base_grand_total'] = //Total já com desconto
	$orderData['grand_total'] = $orderGrandTotal;
	$orderData['total_qty_ordered'] = $orderItems;//Total de itens comprados
	$orderData['weight'] = $orderWeight; //Soma dos pesos de todos os produtos
	$orderData['total_item_count'] = $totalItems; //Total de linhas

	/*try {

		$sql = "UPDATE `sales_flat_order`
					SET ";

		foreach($orderData as $key => $value)
		{
			$sql .= "`{$key}` = '{$value}',";
		}
		$sql = substr($sql, 0, strlen($sql) - 1); //Remover a ultima virgula

		$sql .= " WHERE `entity_id` = {$order->getId()}";

		echo $sql . "\n";

		$write->query($sql);

	} catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}*/

	//#biscoito. Se eu não colocar isso aqui, não encontra o pagamento na hora de salvar o pedido.
	$order->getPayment();

	foreach ($orderData as $key => $value)
	{
		$order->setData($key, $value);
	}

	$order->save();

	return $order;
}
