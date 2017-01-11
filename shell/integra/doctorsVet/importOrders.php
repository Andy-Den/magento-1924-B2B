<?php

require_once './configIntegra.php';
require_once '../_functions/importOrders.php';

$files = array_diff(scandir($testDirectoryImport.'/orders'), array('..', '.'));

const head_line_salesrepid = 0;
const head_line_customerid = 1;
const head_line_paymentmethod = 2;
const head_line_paymentcondition = 3;
const head_line_orderid = 4;
const head_line_orderiderp = 5;
const head_line_datecreate= 6;
const head_line_dateupdate= 7;
const head_line_status = 8;
const head_line_comments = 10; //Adicionar

const item_line_table = 0;
const item_line_qty = 1;
const item_line_iderp = 2;
const item_line_discount = 3;
const item_line_price = 4;



/* Status do ERP da doctorsvet
NONE 0
LIBERADO 1
FATURADO 2
BLOQUEADO POR LIMITE DE CRÉDITO 3
BLOQUEIO PENDENCIA FINANCEIRA 4
BLOQUEIO PENDENCIA DE PEDIDO 5
BLOQUEIO FORA DE ACORDO COMERCIAL 6
BLOQUEIO PARA ANALISE 7
BLOQUEIO CONDIÇÃO ESPECIAL 8
FORÇA DE VENDA 99
ORCAMENTO 200
*/

/* Nossos status
 * 1 = pendente de pagamento
2 = Encaminhado para distribuidora  (compra aprovada)
3 = Nota Fiscal Emitida
4 = Em trânsito
5 = Cancelado
6 = Devolução
7 = Concluído
 */

$splitItemsBySalesrep = false;


//Variáveis usados no arquivo
foreach ($files as $file) {

	debug($file);

	$customer = null;
	$order = null; // Pedido atual
	$status = 0; //Status atual do pedido
	$editOrder = false; //Se alguma coisa no pedido mudar, sera necessario criar outro pedido com os novos dados.
	$orderItems = array(); //Itens da order original
	$fileItems = array(); //Itens  retornados no arquivo de importação
	$totalItems = 0; //Total de itens somados
	$editItems = false;


	if ($local == true):$lines = file("$testDirectoryImport/orders/$file", FILE_IGNORE_NEW_LINES);
	else : $lines = file("$testDirectoryImport/$file", FILE_IGNORE_NEW_LINES); endif;

	foreach ($lines as $index => $line)
	{
		$line = explode('|', $line);
		if ($index == 0)
		{
			$customer = getCustomerByIdErp($line[head_line_customerid]);
			$order = getOrderById($line[head_line_orderid]);
			if (!$order->getId())
			{
				Mage::logException('Pedido ' . $line[head_line_orderid] . ' não encontrado');
				echo 'Pedido ' . $line[head_line_orderid] . ' não encontrado';
				continue;
			}

			//Setar o status do pedido conforme o que está no layout
			switch ($line[head_line_status])
			{
				case '2' : $status = 7;
					break;
			}

			//Salvar o id_erp na order
			if (trim($line[head_line_orderid]) != '') {
				$order->setIdErp($line[head_line_orderiderp]);
				$order->getResource()->saveAttribute($order, 'id_erp');
			}

			//Recuperar o ID do representante do pedido
			$splitItemsBySalesrep = explode('-', $line[head_line_orderid])[1];

			if ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE)
			{
				debug('O pedido já foi finalizado!');
				continue 2;
			}
			else if ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED)
			{
				debug('O pedido já foi cancelado!');
				continue 2;
			}

		}
		else
		{
			$item = Mage::getResourceModel('sales/order_item_collection')
				->addAttributeToFilter('product_id', getProductIdByIdErp($order, $line[item_line_iderp]))
				->addAttributeToFilter('order_id', $order->getId())
				->getFirstItem();

			$orderItems[$line[item_line_iderp]] = $item;

			//Se vier mais itens no arquivo de importação do que tem no pedido, edita o pedido.
			if (!$item->getId())
			{
				$editOrder = true;
			}
			elseif ($status == 3 || $status == 4 || $status == 7)
			{
				if	(
						$item->getQtyOrdered() < $line[item_line_qty] //Se houver mais quantidade de itens do que no pedido, edita o pedido
					)
				{
					$editOrder = true;
				}
				elseif (
						number_format($item->getDiscountPercent(), 2) != $line[item_line_discount] || //Se o desconto for diferente, edita o pedido
						number_format($item->getPrice(), 2) != number_format($line[item_line_price], 2) //Se o preço do produto for diferente, edita o pedido
					)
				{
					$editItems = true;
				}

			}

			$fileItems[$line[item_line_iderp]] = $line;
			$totalItems += $line[item_line_qty];

		}
	}

	//Se houver mudança (para mais) na quantidade dos produtos do pedido
	// Ou se houver adição de pedidos, precisa cancelar a order anterior e criar uma nova.
	if ($editOrder)
	{
		$newOrder = copyOldOrderToNewOrder($order, $fileItems, $customer, $orderItems);
		cancelOrder($order, $fileItems);
		$order = Mage::getModel('sales/order')->load($newOrder['entity_id']);

		//Atualizar a variável de itens do pedido.
		$orderItems = array();
		foreach ($order->getAllItems() as $item)
		{
			$orderItems[$item->getProduct()->getIdErp()] = $item;
		}

	}
	elseif ($splitItemsBySalesrep)
	{
		if ($editItems || getTotalQtyOrderedBySalesrep($order, $splitItemsBySalesrep) > $totalItems || getTotalItemCountBySalesrep($order, $splitItemsBySalesrep) > count($fileItems))
		{
			$order = editOrderItems($order, $fileItems);
		}
	}
	else if ($editItems || $order->getTotalQtyOrdered() > $totalItems || $order->getTotalItemCount() > count($fileItems))
	{
		$order = editOrderItems($order, $fileItems);
	}


	//Pedido faturado, mas ainda nao pago
	if ($status == 3)
	{
		try
		{
			$itemsQty = getItemsQty($orderItems);
			//billOrder($order);
			invoiceOrder($order, $itemsQty);
		}
		catch (Mage_Core_Exception $e)
		{
			Mage::logException($e);
			//mage::throwException(Mage::helper('core')->__('Nao foi possivel adicionar o pedido como faturado: %s', $e->getMessage()));
		}
	}
	elseif ($status == 4) //Pedido foi enviado
	{
		try
		{
			$itemsQty = getItemsQty($orderItems);
			shipOrder($order, $itemsQty);
		}
		catch (Mage_Core_Exception $e)
		{
			Mage::logException($e);
			//mage::throwException(Mage::helper('core')->__('Nao foi possivel efetuar o envio: %s', $e->getMessage()));
		}
	}
	elseif ($status == 5) //O Pedido foi cancelado
	{
		cancelOrder($order, $fileItems);
	}
	elseif ($status == 7) //Pedido completo
	{
		$itemsQty = getItemsQtyBySalesrep($orderItems, $order);
		invoiceOrder($order, $itemsQty);
		completeOrder($order);
	}



}

function editOrderItems($order, $fileItems)
{
	global $splitItemsBySalesrep, $orderItems;

	if ($splitItemsBySalesrep) {
		$items = getOrderItemsBySalesrep($order, $splitItemsBySalesrep);
	} else {
		$items = $order->getAllItems();
	}
	$totalItems = 0;
	//$orderItems = 0;

	foreach ($items as $item)
	{
		$product = $item->getProduct();
		$itemData = array();
		$write = getCoreWrite();

		//Cancelar itens
		if (array_key_exists($product->getIdErp(), $fileItems)) {

			$totalItems++;
			//$orderItems += $fileItems[$product->getIdErp()][item_line_qty];

			$itemData['qty_canceled'] = $item->getQtyOrdered() - $fileItems[$product->getIdErp()][item_line_qty];

			$itemData['updated_at'] = date('Y-m-d H:i:s');
			$itemData['price'] =
			$itemData['base_price'] =
			$itemData['price_incl_tax'] =
			$itemData['base_price_incl_tax'] = $fileItems[$product->getIdErp()][item_line_price];
			$itemData['discount_percent'] = ($fileItems[$product->getIdErp()][item_line_qty] - $itemData['qty_canceled'] > 0) ? $fileItems[$product->getIdErp()][item_line_discount] : 0;
			$itemData['discount_amount'] =
			$itemData['base_discount_amount'] =	($fileItems[$product->getIdErp()][item_line_qty] - $itemData['qty_canceled'] > 0) ? abs(($fileItems[$product->getIdErp()][item_line_price] * ((100-$fileItems[$product->getIdErp()][item_line_discount]) / 100)) - $fileItems[$product->getIdErp()][item_line_price]) : 0;
			$itemData['row_total'] =
			$itemData['base_row_total'] =
			$itemData['row_total_incl_tax'] =
			$itemData['base_row_total_incl_tax'] = $fileItems[$product->getIdErp()][item_line_price] * $fileItems[$product->getIdErp()][item_line_qty];
			$itemData['row_weight'] = $item->getWeight() * $fileItems[$product->getIdErp()][item_line_qty];

		}
		else
		{
			$itemData['qty_canceled'] = $item->getQtyOrdered();
			$itemData['discount_percent'] =
			$itemData['discount_amount'] =
			$itemData['base_discount_amount'] =
			$itemData['row_total'] =
			$itemData['base_row_total'] =
			$itemData['row_total_incl_tax'] =
			$itemData['base_row_total_incl_tax'] =
			$itemData['row_weight'] = 0;
		}

		foreach ($itemData as $key => $value)
		{
			$item->setData($key, $value);
		}

		$item->save();

		$orderItems[$product->getIdErp()] = $item;

		debug('Item editado: ' . implode(', ', $itemData));

	}

	$order = updateOrderPrices($order);
	$order->save();

	return $order;
}


function copyOldOrderToNewOrder($order, $fileItems, $customer, $orderItems)
{

	global $websiteId, $stockId, $splitItemsBySalesrep;

	$write = getCoreWrite();

	//Calcular o valor total dos produtos
	$subtotal = 0;
	$discount_amount = 0;
	$qty_ordered = 0;
	$weight = 0;
	$itemsQtys = array();
	foreach ($fileItems as $item)
	{
 		$item['subtotal'] = $item[item_line_price] * $item[item_line_qty];
		$item['price_with_discount'] = $item['subtotal'] * ((100 - $item[item_line_discount]) / 100);
		$item['discount_amount'] = $item['subtotal'] - $item['price_with_discount'];

		$subtotal += $item['subtotal'];
		$discount_amount += $item['discount_amount'];
		$qty_ordered += $item[item_line_qty];
		$weight += $orderItems[$item[item_line_iderp]]['row_weight'];

		$itemsQtys[getProductIdByIdErp($order, $item[item_line_iderp])] = $item[item_line_qty];
	}

	$grand_total = $subtotal - $discount_amount;

	$sql = "SELECT *
			FROM	`sales_flat_quote`
			WHERE  entity_id = {$order->getQuoteId()}
			";
	$result = $write->query($sql);
	$newQuote = $result->fetch();

	unset($newQuote['entity_id']);
	$newQuote['created_at'] =
	$newQuote['updated_at'] = date('Y-m-d H:i:s');
	$newQuote['items_count'] = count($fileItems);
	$newQuote['items_qty'] = $qty_ordered;
	$newQuote['grand_total'] =
	$newQuote['base_grand_total'] =
	$newQuote['subtotal_with_discount'] =
	$newQuote['base_subtotal_with_discount'] = $grand_total;
	$newQuote['reserved_order_id'] = NULL;
	$newQuote['subtotal'] =
	$newQuote['base_subtotal'] = $subtotal;

	$newQuote['items_qtys'] = 'a:'.count($itemsQtys).':{';
	foreach ($itemsQtys as $key => $value)
	{
		$newQuote['items_qtys'] .= 'i:'.$key.';d:'.$value.';';
	}
	$newQuote['items_qtys'] .= '}';

	//Salva a quote
	try {

		$sql = "INSERT INTO sales_flat_quote
			(".implode(', ', array_keys($newQuote)).")
			VALUES
			 ('".implode("', '", $newQuote)."')
		";

		$result = $write->query($sql);

		$newQuote['entity_id'] = $write->lastInsertId();

	}
	catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}

	//Salva os produtos da quote
	foreach ($fileItems as $item) {
		$orderItem = $orderItems[$item[item_line_iderp]]->getData();
		if (!isset($orderItem['product_id'])) {
			$orderItem = getProductByIdErp($item[item_line_iderp])->getData();
			$orderItem['product_id'] = $orderItem['entity_id'];
			$orderItem['product_type'] = $orderItem['type_id'];
			$orderItem['weight'] = $orderItem['weight'];
			$orderItem['applied_rule_ids'] =
			$orderItem['additional_data'] = NULL;
			$orderItem['original_price'] = $orderItem['price'];

			$orderItem['stock_id'] = $stockId;
		}


		$itemData = array();

		$itemData['quote_id'] = $newQuote['entity_id'];
		$itemData['created_at'] =
		$itemData['updated_at'] = date('Y-m-d H:i:s');
		$itemData['product_id'] = $orderItem['product_id'];
		$itemData['store_id'] = $newQuote['store_id'];
		$itemData['sku'] = $orderItem['sku'];
		$itemData['name'] = $orderItem['name'];
		$itemData['description'] = '';
		$itemData['weight'] = $orderItem['weight'];
		$itemData['qty'] = $item[item_line_qty];
		$itemData['price'] =
		$itemData['base_price'] =
		$itemData['price_incl_tax'] = $item[item_line_price];
		$itemData['discount_percent'] = $item[item_line_discount];
		$itemData['discount_amount'] =
		$itemData['base_discount_amount'] = abs(($item[item_line_price] * ((100 - $item[item_line_discount]) / 100)) - $item[item_line_price]) * $item[item_line_qty];
		$itemData['row_total'] =
		$itemData['base_row_total'] =
		$itemData['row_total_with_discount'] =
		$itemData['row_total_incl_tax'] =
		$itemData['base_row_total_incl_tax'] = $item[item_line_price] * $item[item_line_qty];
		$itemData['row_weight'] = $orderItem['weight'] * $item[item_line_qty];
		$itemData['product_type'] = $orderItem['product_type'];
		$itemData['weee_tax_applied'] = 'a:0:{}';
		$itemData['product_width'] = $orderItem['product_width'];
		$itemData['product_length'] = $orderItem['product_length'];
		$itemData['product_height'] = $orderItem['product_height'];
		$itemData['stock_id'] = $orderItem['stock_id'];

		if ($splitItemsBySalesrep)
		{
			$itemData['salesrep_id'] = $splitItemsBySalesrep;
		}


		//Salva o produto
		try {

			$sql = "INSERT INTO sales_flat_quote_item
			(".implode(', ', array_keys($itemData)).")
			VALUES
			 ('".implode("', '", $itemData)."')
		";

			$result = $write->query($sql);

			$itemData['item_id'] = $write->lastInsertId();

		}
		catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			mage::throwException($e->getMessage());
		}
	}

	//Salva o pagamento da quote
	$sql = "SELECT *
			FROM `sales_flat_quote_payment`
			WHERE quote_id = {$order->getQuoteId()}
		";
	$result = $write->query($sql);
	$payment = $result->fetch();

	unset($payment['payment_id']);
	$payment['quote_id'] = $newQuote['entity_id'];

	try {
		$sql = "INSERT INTO sales_flat_quote_payment
				(".implode(', ', array_keys($payment)).")
				VALUES
				 ('".implode("', '", $payment)."')
			";
		$result = $write->query($sql);
	}
	catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}


	//Seta os valores default da order atual
	$newOrder = $order->getData();
	unset($newOrder['entity_id']);
	unset($newOrder['payment_authorization_expiration']);
	unset($newOrder['forced_do_shipment_with_invoice']);
	unset($newOrder['base_shipping_hidden_tax_amount']);

	$newOrder['base_subtotal'] = //Total sem desconto
	$newOrder['base_subtotal_incl_tax'] =
	$newOrder['subtotal'] =
	$newOrder['subtotal_incl_tax'] = $subtotal;
	$newOrder['base_discount_amount'] =  //Total do desconto somado
	$newOrder['discount_amount'] = $discount_amount;
	$newOrder['base_grand_total'] = //Total já com desconto
	$newOrder['grand_total'] = $grand_total;
	$newOrder['total_qty_ordered'] = $qty_ordered;//Total de itens comprados
	$newOrder['quote_id'] = $newQuote['entity_id'];
	$newOrder['weight'] = $weight; //Soma dos pesos de todos os produtos
	$newOrder['total_item_count'] = count($fileItems); //Total de linhas

	$newOrder['state'] = 'new';
	$newOrder['status'] = 'pending';
	$newOrder['protect_code'] = substr(md5($order->getProtectCode()), 0, 6);

	$i = 1;
	$tmpOrder = NULL;
	do {
		$newOrder['increment_id'] =  $order->getIncrementId().'-'.($order->getEditIncrement()+$i++);
		$tmpOrder = Mage::getModel('sales/order')->loadByIncrementId($newOrder['increment_id']);
	} while($tmpOrder->getId());

	//$newOrder['increment_id'] =  $order->getIncrementId().'-'.($order->getEditIncrement()+1);
	$newOrder['original_increment_id'] = $order->getIncrementId();
	$newOrder['relation_parent_id'] = $order->getId();
	$newOrder['relation_parent_real_id'] = $order->getIncrementId();
	$newOrder['created_at'] = date('Y-m-d H:i:s');
	$newOrder['updated_at'] = date('Y-m-d H:i:s');

	//Set NULL
	$newOrder['base_total_due'] =
	$newOrder['total_due'] =
	$newOrder['base_total_offline_refunded'] =
	$newOrder['base_total_online_refunded'] =
	$newOrder['base_total_paid'] =
	$newOrder['base_total_qty_ordered'] =
	$newOrder['base_total_refunded'] =
	$newOrder['base_total_canceled'] =
	$newOrder['base_total_invoiced'] =
	$newOrder['base_total_invoiced_cost'] =
	$newOrder['base_discount_canceled'] =
	$newOrder['base_discount_invoiced'] =
	$newOrder['base_discount_refunded'] =
	$newOrder['base_shipping_canceled'] =
	$newOrder['base_shipping_invoiced'] =
	$newOrder['base_shipping_refunded'] =
	$newOrder['base_subtotal_canceled'] =
	$newOrder['base_subtotal_invoiced'] =
	$newOrder['base_subtotal_refunded'] =
	$newOrder['discount_canceled'] =
	$newOrder['discount_invoiced'] =
	$newOrder['discount_refunded'] =
	$newOrder['shipping_canceled'] =
	$newOrder['shipping_invoiced'] =
	$newOrder['shipping_refunded'] =
	$newOrder['shipping_tax_refunded'] =
	$newOrder['subtotal_canceled'] =
	$newOrder['subtotal_invoiced'] =
	$newOrder['subtotal_refunded'] =
	$newOrder['tax_canceled'] =
	$newOrder['tax_invoiced'] =
	$newOrder['tax_refunded'] =
	$newOrder['total_canceled'] =
	$newOrder['total_invoiced'] =
	$newOrder['total_offline_refunded'] =
	$newOrder['total_online_refunded'] =
	$newOrder['total_paid'] =
	$newOrder['total_refunded'] =
	$newOrder['email_sent'] =
	$newOrder['quote_address_id'] =
	$newOrder['adjustment_negative'] =
	$newOrder['adjustment_positive'] =
	$newOrder['base_adjustment_negative'] =
	$newOrder['base_adjustment_positive'] = 'NULL';

	//Set Zero
	$newOrder['base_shipping_amount'] =
	$newOrder['base_shipping_tax_amount'] =
	$newOrder['base_shipping_tax_refunded'] =
	$newOrder['base_tax_amount'] =
	$newOrder['base_tax_canceled'] =
	$newOrder['base_tax_invoiced'] =
	$newOrder['base_tax_refunded'] =
	$newOrder['shipping_amount'] =
	$newOrder['shipping_tax_amount'] =
	$newOrder['tax_amount'] =
	$newOrder['customer_note_notify'] =
	$newOrder['base_shipping_discount_amount'] =
	$newOrder['shipping_discount_amount'] = 0;

	//Set One
	$newOrder['base_to_global_rate'] =
	$newOrder['store_to_base_rate'] = 1;

	//Salva o pedido
	try {

		$sql = "INSERT INTO sales_flat_order
			(".implode(', ', array_keys($newOrder)).")
			VALUES
			 ('".implode("', '", $newOrder)."')
		";

		$result = $write->query($sql);

		$newOrder['entity_id'] = $write->lastInsertId();

	}
	catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}

	//Atualiza o pedido antigo, inserindo os dados do novo pedido
	try {
		$write->query("UPDATE `sales_flat_order`
			SET `relation_child_id` = '{$newOrder['entity_id']}',
				`relation_child_real_id` = '{$newOrder['increment_id']}'
			WHERE `sales_flat_order`.`entity_id` = '{$order->getId()}';
		");
	}
	catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}

	//Salva os produtos do pedido
	foreach ($fileItems as $item)
	{
		$orderItem = $orderItems[$item[item_line_iderp]]->getData();
		if (!isset($orderItem['product_id']))
		{
			$orderItem = getProductByIdErp($item[item_line_iderp])->getData();
			$orderItem['product_id'] = $orderItem['entity_id'];
			$orderItem['product_type'] = $orderItem['type_id'];
			$orderItem['weight'] = $orderItem['weight'];
			$orderItem['applied_rule_ids'] =
			$orderItem['additional_data'] = NULL;
			$orderItem['original_price'] = $orderItem['price'];

			$orderItem['stock_id'] = $stockId;
		}


		$itemData = array();

		$itemData['order_id'] = $newOrder['entity_id'];
		$itemData['quote_item_id'] = '';
		$itemData['store_id'] = $newOrder['store_id'];
		$itemData['created_at'] = $itemData['updated_at'] = date('Y-m-d H:i:s');
		$itemData['product_id'] = $orderItem['product_id'];
		$itemData['product_type'] = $orderItem['product_type'];
		$itemData['product_options'] = $orderItem['product_options'];
		$itemData['weight'] = $orderItem['weight'];
		$itemData['sku'] = $orderItem['sku'];
		$itemData['name'] = $orderItem['name'];
		$itemData['description'] = '';//$orderItem['description'];
		$itemData['applied_rule_ids'] = $orderItem['applied_rule_ids'];
		$itemData['additional_data'] = $orderItem['additional_data'];
		$itemData['qty_ordered'] = $item[item_line_qty];
		$itemData['price'] =
		$itemData['base_price'] =
		$itemData['price_incl_tax'] =
		$itemData['base_price_incl_tax'] = $item[item_line_price];
		$itemData['original_price'] =
		$itemData['base_original_price'] = $orderItem['original_price'];
		$itemData['discount_percent'] = $item[item_line_discount];
		$itemData['discount_amount'] =
		$itemData['base_discount_amount'] =	abs(($item[item_line_price] * ((100-$item[item_line_discount]) / 100)) - $item[item_line_price]) * $item[item_line_qty];
		$itemData['row_total'] =
		$itemData['base_row_total'] =
		$itemData['row_total_incl_tax'] =
		$itemData['base_row_total_incl_tax'] = $item[item_line_price] * $item[item_line_qty];
		$itemData['row_weight'] = $orderItem['weight'] * $item[item_line_qty];
		$itemData['weee_tax_applied'] = 'a:0:{}';
		$itemData['product_width'] = $orderItem['product_width'];
		$itemData['product_length'] = $orderItem['product_length'];
		$itemData['product_height'] = $orderItem['product_height'];
		$itemData['stock_id'] = $orderItem['stock_id'];

		//Set Zero
		$itemData['qty_backordered'] =
		$itemData['qty_canceled'] =
		$itemData['qty_invoiced'] =
		$itemData['qty_refunded'] =
		$itemData['qty_shipped'] =
		$itemData['qty_shipped'] =
		$itemData['tax_percent'] =
		$itemData['tax_amount'] =
		$itemData['base_tax_amount'] =
		$itemData['tax_invoiced'] =
		$itemData['base_tax_invoiced'] =
		$itemData['discount_invoiced'] =
		$itemData['base_discount_invoiced'] =
		$itemData['amount_refunded'] =
		$itemData['base_amount_refunded'] =
		$itemData['row_invoiced'] =
		$itemData['hidden_tax_amount'] =
		$itemData['is_nominal'] =
		$itemData['hidden_tax_canceled'] =
		$itemData['base_weee_tax_applied_amount'] =
		$itemData['base_weee_tax_applied_row_amnt'] =
		$itemData['weee_tax_applied_amount'] =
		$itemData['weee_tax_applied_row_amount'] =
		$itemData['weee_tax_disposition'] =
		$itemData['weee_tax_row_disposition'] =
		$itemData['weee_tax_row_disposition'] =
		$itemData['base_weee_tax_disposition'] =
		$itemData['base_weee_tax_row_disposition'] =
		$itemData['is_qty_decimal'] =
		$itemData['free_shipping'] =
		$itemData['is_virtual'] =
		$itemData['no_discount'] = 0;

		//Set  Null
		$itemData['base_tax_before_discount'] =
		$itemData['tax_before_discount'] =
		$itemData['ext_order_item_id'] =
		$itemData['locked_do_invoice'] =
		$itemData['locked_do_ship'] =
		$itemData['hidden_tax_invoiced'] =
		$itemData['hidden_tax_refunded'] =
		$itemData['tax_refunded'] =
		$itemData['base_tax_refunded'] =
		$itemData['discount_refunded'] =
		$itemData['base_discount_refunded'] =
		$itemData['gift_message_id'] =
		$itemData['gift_message_available'] = NULL;

		if ($splitItemsBySalesrep)
		{
			$itemData['salesrep_id'] = $splitItemsBySalesrep;
		}


		//Salva o produto
		try {

			$sql = "INSERT INTO sales_flat_order_item
			(".implode(', ', array_keys($itemData)).")
			VALUES
			 ('".implode("', '", $itemData)."')
		";

			$result = $write->query($sql);

			//$itemData['item_id'] = $write->lastInsertId();

		}
		catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			mage::throwException($e->getMessage());
		}

	}

	//Salva o pagamento do pedido
	$sql = "SELECT *
			FROM `sales_flat_order_payment`
			WHERE parent_id = {$order->getId()}
		";
	$result = $write->query($sql);
	$payment = $result->fetch();

	unset($payment['entity_id']);
	$payment['parent_id'] = $newOrder['entity_id'];

	try {
		$sql = "INSERT INTO sales_flat_order_payment
				(".implode(', ', array_keys($payment)).")
				VALUES
				 ('".implode("', '", $payment)."')
			";
		$result = $write->query($sql);
	}
	catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}

	//Salva os endereços do produto
	$sql = "SELECT *
			FROM `sales_flat_order_address`
			WHERE parent_id = {$order->getId()}
		";
	$results = $write->fetchAll($sql);

	foreach ($results as $address)
	{
		unset($address['entity_id']);
		$address['stock_id'] = $stockId;
		$address['parent_id'] = $newOrder['entity_id'];

		try {
			$sql = "INSERT INTO sales_flat_order_address
					(" . implode(', ', array_keys($address)) . ")
					VALUES
					 ('" . implode("', '", $address) . "')
				";
			$result = $write->query($sql);
		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			mage::throwException($e->getMessage());
		}
	}

	//Adicionar status da order
	try {
		$sql = "INSERT INTO sales_flat_order_status_history
				(parent_id, is_customer_notified, is_visible_on_front, comment, status, created_at, entity_name)
				VALUES
				 ({$newOrder['entity_id']}, 0, 0, 'Pedido criado via integração', '{$newOrder['status']}', '{$newOrder['created_at']}', 'order')
			";
		$write->query($sql);
	}
	catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}

	//Adicionar a order Grid
	$sql = "SELECT *
			FROM `sales_flat_order_grid`
			WHERE entity_id = {$order->getId()}
		";
	$result = $write->query($sql);
	$grid = $result->fetch();

	$grid['entity_id'] = $newOrder['entity_id'];
	$grid['status'] = $newOrder['status'];
	$grid['base_grand_total'] = $newOrder['base_grand_total'];
	$grid['grand_total'] = $newOrder['grand_total'];
	$grid['increment_id'] = $newOrder['increment_id'];
	$grid['created_at'] =
	$grid['updated_at'] = date('Y-m-d H:i:s');

	try {
		$sql = "INSERT INTO sales_flat_order_grid
				(".implode(', ', array_keys($grid)).")
				VALUES
				 ('".implode("', '", $grid)."')
			";
		$write->query($sql);
	}
	catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}


	//Adicionar a order na warehouse
	try {
		$sql = "INSERT INTO warehouse_flat_order_grid_warehouse
				(entity_id, stock_id)
				VALUES
				 ({$newOrder['entity_id']}, {$stockId})
			";
		$write->query($sql);
	}
	catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}

	//Adicionar a order Grid
	$sql = "SELECT *
			FROM `allpago_payment_orders`
			WHERE order_id = {$order->getId()}
		";
	$result = $write->query($sql);
	$allpago = $result->fetch();

	unset($allpago['id']);
	$allpago['order_id'] = $newOrder['entity_id'];

	foreach($allpago as $key => $value)
	{
		if ($value == '')
		{
			unset($allpago[$key]);
		}
	}

	try {
		$sql = "INSERT INTO allpago_payment_orders
				(".implode(', ', array_keys($allpago)).")
				VALUES
				 ('".implode("', '", $allpago)."')
			";
		$write->query($sql);
	}
	catch (Mage_Core_Exception $e) {
		Mage::logException($e);
		mage::throwException($e->getMessage());
	}

	debug('Order duplicada.');

	$order = new Varien_Object();
	return $order->setData($newOrder);

}