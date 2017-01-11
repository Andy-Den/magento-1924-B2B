<?php

require_once './configIntegra.php';
require_once '../_functions/importOrders.php';

$files = array_diff(scandir($testDirectoryImport.'/orders'), array('..', '.'));

const head_line_orderid = 0;
const head_line_salesrepid = 1;
const head_line_customerid = 2;
const head_line_paymentmethod = 3;
const head_line_paymentcondition = 4;
const head_line_status = 8;
const head_line_table = 9;

const item_line_qty = 0;
const item_line_iderp = 1;
const item_line_discount = 2;
const item_line_price = 3;

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
	else : $lines = file("$testDirectoryImport/orders/$file", FILE_IGNORE_NEW_LINES); endif;

	//Flag para saber se deve dividir os itens por representante.
	$splitItemsBySalesrep = NULL;
	$splitItemsByTableprice = NULL;

	foreach ($lines as $index => $line)
	{
		$line = explode('|', $line);
		if ($index == 0)
		{
			$customer = getCustomerByIdErp(explode('-', $line[head_line_orderid])[0]);
			$order = getOrderById($line[head_line_orderid]);

			if (!$order->getId())
			{
				debug('Pedido nao encontrado!');
				continue 2;
			}

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

			$status = $line[head_line_status];

			$splitItemsBySalesrep = explode('-', $line[head_line_orderid])[1];
			$splitItemsByTableprice = $line[head_line_table];
		}
		else
		{

			$item = Mage::getResourceModel('sales/order_item_collection')
				->addAttributeToFilter('product_id', getProductIdByIdErp($order, $line[item_line_iderp]))
				->addAttributeToFilter('order_id', $order->getId())
				->getFirstItem();

			$orderItems[$line[item_line_iderp]] = $item;
			$line[item_line_price] = str_replace(',', '.', str_replace('.', '', $line[item_line_price])); //Converte preços com , para preços com .
			$line[item_line_discount] = str_replace(',', '.', str_replace('.', '', $line[item_line_discount])); //Converte preços com , para preços com .

			//Se vier mais itens no arquivo de importação do que tem no pedido, edita o pedido.
			if (!$item->getId())
			{
				$editOrder = true;
			}
			elseif ($status == '3' || $status == '4' || $status == '7')
			{
				if	(
						$item->getQtyOrdered() < $line[item_line_qty] //Se houver mais quantidade de itens do que no pedido, edita o pedido
					)
				{
					$editOrder = true;
					debug('Item: ' . $item->getSku() . ' Quantidade maior do que do pedido: ' . $item->getQtyOrdered() . ' < ' . $line[item_line_qty]);
				}
				elseif (
						number_format($item->getDiscountPercent(), 2) != $line[item_line_discount] || //Se o desconto for diferente, edita o pedido
						number_format($item->getPrice(), 2) != $line[item_line_price] //Se o preço do produto for diferente, edita o pedido
					)
				{
					$editItems = true;
					debug('Item: ' . $item->getSku() . ' Valores divergentes: ' . number_format($item->getDiscountPercent(), 2) . ' != ' . $line[item_line_discount] . ' | ' . number_format($item->getPrice(), 2) . ' != ' . $line[item_line_price]);
				}
				else
				{
					debug('Item: ' . $item->getSku() . '|' . $line[item_line_iderp] . ' OK!');
				}

			}


			$fileItems[$line[item_line_iderp]] = $line;
			$totalItems += $line[item_line_qty];

		}
	}


	//Se tiver necessidade de editar a order
	//É necessário criar um aviso
	if ($editOrder)
	{
		continue;
		debug('É necessário alterar o pedido. Opção não implementada');
	}

	if ($editItems || getTotalQtyOrdered($order, $splitItemsBySalesrep, $splitItemsByTableprice) > $totalItems || getTotalItemCount($order, $splitItemsBySalesrep, $splitItemsByTableprice) > count($fileItems))
	{
		$order = editOrderItems($order, $fileItems);
	}

	if ($status == 5) //O Pedido foi cancelado
	{
		cancelOrder($order, $fileItems);
	}
	elseif ($status == 7) //Pedido completo
	{
		$itemsQty = getAllItemsQty($orderItems, $order);
		invoiceOrder($order, $itemsQty);
		//$itemsQty = getItemsQtyBySalesrep($orderItems, $order, false);
		//shipOrder($order, $itemsQty);
		completeOrder($order);
	}
}

function editOrderItems($order, $fileItems)
{
	global $splitItemsBySalesrep, $orderItems;

	if (isset($splitItemsBySalesrep)) {
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
			$itemData['discount_percent'] = $fileItems[$product->getIdErp()][item_line_discount];
			$itemData['discount_amount'] =
			$itemData['base_discount_amount'] =	abs(($fileItems[$product->getIdErp()][item_line_price] * ((100-$fileItems[$product->getIdErp()][item_line_discount]) / 100)) - $fileItems[$product->getIdErp()][item_line_price]);
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

	}

	$order = updateOrderPrices($order);
	$order->save();

	return $order;
}