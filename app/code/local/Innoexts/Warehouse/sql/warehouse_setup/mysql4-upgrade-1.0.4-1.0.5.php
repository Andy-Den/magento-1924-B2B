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

$installer                         = $this;
$connection                        = $installer->getConnection();
$catalogProductTable               = $installer->getTable('catalog/product');
$catalogInventoryStockTable        = $installer->getTable('cataloginventory/stock');
$catalogProductStockPriceTable     = $installer->getTable('catalog/product_stock_price');

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$catalogProductStockPriceTable}`;

CREATE TABLE `{$catalogProductStockPriceTable}` (
  `product_id` int(10) unsigned not null, 
  `stock_id` smallint(6) unsigned not null, 
  `price` decimal(12,4) NOT NULL default '0.00', 
  `price_type` enum('fixed', 'percent') NOT NULL default 'fixed', 
  PRIMARY KEY  (`product_id`, `stock_id`), 
  KEY `FK_CATALOG_PRODUCT_STOCK_PRICE_PRODUCT` (`product_id`), 
  KEY `FK_CATALOG_PRODUCT_STOCK_PRICE_STOCK` (`stock_id`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_STOCK_PRICE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$catalogProductTable} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_STOCK_PRICE_STOCK` FOREIGN KEY (`stock_id`) REFERENCES {$catalogInventoryStockTable} (`stock_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();