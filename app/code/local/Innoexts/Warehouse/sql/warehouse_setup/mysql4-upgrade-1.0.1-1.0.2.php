<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

$installer                     = $this;
$connection                    = $installer->getConnection();
$warehouseTable                = $installer->getTable('warehouse');
$catalogProductTable           = $installer->getTable('catalog/product');
$catalogProductShelfTable      = $installer->getTable('catalog/product_shelf');

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$catalogProductShelfTable}`;

CREATE TABLE `{$catalogProductShelfTable}` (
  `product_id` int(10) unsigned not null, 
  `warehouse_id` smallint(6) unsigned not null, 
  `name` varchar(128) not null default '', 
  PRIMARY KEY  (`product_id`, `warehouse_id`, `name`), 
  KEY `FK_CATALOG_PRODUCT_SHELF_PRODUCT` (`product_id`), 
  KEY `FK_CATALOG_PRODUCT_SHELF_WAREHOUSE` (`warehouse_id`), 
  KEY `IDX_NAME` (`name`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_SHELF_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$catalogProductTable} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_SHELF_WAREHOUSE` FOREIGN KEY (`warehouse_id`) REFERENCES {$warehouseTable} (`warehouse_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
