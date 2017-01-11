<?php

$installer = $this;
$installer->startSetup();

$warehouseTable	= $installer->getTable('warehouse/warehouse');

// Criar warehouse da Peclam

//Create stock for warehouses
$installer->run("
	INSERT INTO `cataloginventory_stock` (`stock_name`)
	SELECT cs.name
	FROM core_store as cs
	WHERE cs.code = 'peclam'
	AND NOT EXISTS (
    	SELECT cataloginventory_stock.stock_name FROM cataloginventory_stock WHERE cataloginventory_stock.stock_name = cs.name
	)
	GROUP BY cs.store_id
;");

//Create warehouses with existing websites
$installer->run("
	INSERT INTO `{$warehouseTable}` (`code`, `title`, `description`, `stock_id`, `priority`, `notify`, `contact_name`, `contact_email`)
	SELECT cs.code, cs.name, NULL, cis.stock_id, '0', '1', cs.name, CONCAT('ti+',cs.code,'@4vets.com.br')
	FROM core_store as cs, cataloginventory_stock as cis
	WHERE cs.code = 'peclam'
	AND cis.stock_name = cs.name
	AND NOT EXISTS (
    	SELECT `{$warehouseTable}`.code FROM `{$warehouseTable}` WHERE `{$warehouseTable}`.code = cs.code
	)
	GROUP BY cs.store_id
;");

//Insert storeviews into warehouses

$installer->run("
	INSERT INTO `warehouse_store` (`warehouse_id`, `store_id`)
	SELECT w.warehouse_id, cs.store_id
	FROM `{$warehouseTable}` as w, core_store as cs
	WHERE w.code = 'peclam'
	AND cs.code = 'peclam'
	AND NOT EXISTS (
    	SELECT `warehouse_store`.warehouse_id FROM `warehouse_store` WHERE `warehouse_store`.warehouse_id = w.warehouse_id AND `warehouse_store`.store_id = cs.store_id
	)
;");

//Update products stocks

$installer->run("
	INSERT INTO cataloginventory_stock_item
	SELECT null, cpe.entity_id as product_id, w.stock_id, cisi.qty, cisi.min_qty, cisi.use_config_min_qty, cisi.is_qty_decimal, cisi.backorders, cisi.use_config_backorders, cisi.min_sale_qty, cisi.use_config_min_sale_qty, cisi.max_sale_qty, cisi.use_config_max_sale_qty, cisi.is_in_stock, cisi.low_stock_date, cisi.notify_stock_qty, cisi.use_config_notify_stock_qty, cisi.manage_stock, cisi.use_config_manage_stock, cisi.stock_status_changed_auto, cisi.use_config_qty_increments, cisi.qty_increments, cisi.use_config_enable_qty_inc, cisi.enable_qty_increments, cisi.is_decimal_divided
	FROM `eav_entity_type` AS eet, catalog_product_entity as cpe, catalog_product_website as cpw, warehouse_store as ws, core_store as cs, warehouse as w, cataloginventory_stock_item as cisi
	WHERE eet.entity_type_code = 'catalog_product'
	AND eet.entity_type_id = cpe.entity_type_id
	AND cpw.product_id = cpe.entity_id
	AND cpw.website_id = cs.website_id
	AND cs.store_id = ws.store_id
	AND w.warehouse_id = ws.warehouse_id
	AND cpw.website_id >= 2
	AND cisi.stock_id = 1
	AND cisi.product_id = cpe.entity_id
	AND NOT EXISTS (
    	SELECT `cataloginventory_stock_item`.* FROM `cataloginventory_stock_item` WHERE `cataloginventory_stock_item`.product_id = cpe.entity_id AND `cataloginventory_stock_item`.stock_id = w.stock_id
	)
;");

$installer->endSetup();