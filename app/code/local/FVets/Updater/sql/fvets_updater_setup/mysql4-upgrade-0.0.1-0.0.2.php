<?php

$installer = $this;
$installer->startSetup();

$warehouseTable	= $installer->getTable('warehouse/warehouse');

// Criar warehouse da Omega

//Create stock for warehouses
$installer->run("
	INSERT INTO `cataloginventory_stock` (`stock_name`)
	SELECT cs.name
	FROM core_store as cs
	WHERE cs.code = 'omega'
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
	WHERE cs.code = 'omega'
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
	WHERE w.code = 'omega'
	AND (cs.code = 'omega' OR cs.code = 'omegamsd' OR cs.code = 'omegapurina' OR cs.code = 'omega_msd')
	AND NOT EXISTS (
    	SELECT `warehouse_store`.warehouse_id FROM `warehouse_store` WHERE `warehouse_store`.warehouse_id = w.warehouse_id AND `warehouse_store`.store_id = cs.store_id
	)
;");

$installer->endSetup();