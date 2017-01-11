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

$installer                          = $this;
$connection                         = $installer->getConnection();

$helper                             = Mage::helper('warehouse');
$stockTable                         = $installer->getTable('cataloginventory/stock');
$productTable                       = $installer->getTable('catalog/product');
$productStockPriorityTable          = $installer->getTable('catalog/product_stock_priority');
$productStockShippingCarrierTable   = $installer->getTable('catalog/product_stock_shipping_carrier');

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS `{$productStockPriorityTable}`;

CREATE TABLE `{$productStockPriorityTable}` (
  `product_id` int(10) unsigned not null, 
  `stock_id` smallint(6) unsigned not null, 
  `priority` smallint(6) unsigned not null default 0, 
  PRIMARY KEY  (`product_id`, `stock_id`), 
  KEY `FK_CATALOG_PRODUCT_STOCK_PRIORITY_PRODUCT` (`product_id`), 
  KEY `FK_CATALOG_PRODUCT_STOCK_PRIORITY_STOCK` (`stock_id`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_STOCK_PRIORITY_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$productTable} (`entity_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_STOCK_PRIORITY_STOCK` FOREIGN KEY (`stock_id`) REFERENCES {$stockTable} (`stock_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
-- DROP TABLE IF EXISTS `{$productStockShippingCarrierTable}`;

CREATE TABLE `{$productStockShippingCarrierTable}` (
  `product_id` int(10) unsigned not null, 
  `stock_id` smallint(6) unsigned not null, 
  `shipping_carrier` varchar(255) not null, 
  PRIMARY KEY  (`product_id`, `stock_id`, `shipping_carrier`), 
  KEY `FK_CATALOG_PRODUCT_STOCK_SHIPPING_CARRIER_PRODUCT` (`product_id`), 
  KEY `FK_CATALOG_PRODUCT_STOCK_SHIPPING_CARRIER_STOCK` (`stock_id`), 
  KEY `IDX_SHIPPING_CARRIER` (`shipping_carrier`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_STOCK_SHIPPING_CARRIER_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES {$productTable} (`entity_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_STOCK_SHIPPING_CARRIER_STOCK` FOREIGN KEY (`stock_id`) REFERENCES {$stockTable} (`stock_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->endSetup();