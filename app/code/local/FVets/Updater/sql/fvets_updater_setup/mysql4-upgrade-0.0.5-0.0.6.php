<?php

/**
 * Inclui os clientes da doctorsvet nos grupos de acesso corretos
 */

$installer = $this;

$installer->startSetup();

//Atualiza os itens do pedido 1080 que estao com o representante errado
$installer->run("
	UPDATE `sales_flat_order_item` SET `salesrep_id` = '67' WHERE `sales_flat_order_item`.`item_id` =7937;
	UPDATE `sales_flat_order_item` SET `salesrep_id` = '67' WHERE `sales_flat_order_item`.`item_id` =7938;
");

//Atualizar os pedidos para que nao sejam exportados
$installer->run("
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =936;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =909;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =872;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =844;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =837;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =821;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =443;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =442;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =696;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =457;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =456;
	UPDATE `sales_flat_order` SET `exported` = '2' WHERE `sales_flat_order`.`entity_id` =431;
");



$installer->endSetup();

