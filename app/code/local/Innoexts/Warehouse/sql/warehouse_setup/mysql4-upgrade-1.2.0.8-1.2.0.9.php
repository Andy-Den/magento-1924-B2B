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
$installer                                      = $this;

$productTable                                   = $installer->getTable('catalog/product');
$productStockTaxClassTable                      = $installer->getTable('catalog/product_stock_tax_class');
$stockTable                                     = $installer->getTable('cataloginventory/stock');
$taxClassTable                                  = $installer->getTable('tax/tax_class');

$installer->startSetup();

/**
 * Product Stock Tax Class
 */
$installer->run("
CREATE TABLE `{$productStockTaxClassTable}` (
  `product_id` int(10) unsigned not null, 
  `stock_id` smallint(6) unsigned not null, 
  `tax_class_id` smallint(6) null, 
  PRIMARY KEY  (`product_id`, `stock_id`), 
  KEY `FK_CATALOG_PRODUCT_STOCK_TAX_CLASS_PRODUCT_ID` (`product_id`), 
  KEY `FK_CATALOG_PRODUCT_STOCK_TAX_CLASS_STOCK_ID` (`stock_id`), 
  KEY `FK_CATALOG_PRODUCT_STOCK_TAX_CLASS_TAX_CLASS_ID` (`tax_class_id`), 
  CONSTRAINT `FK_CATALOG_PRODUCT_STOCK_TAX_CLASS_PRODUCT_ID` 
    FOREIGN KEY (`product_id`) REFERENCES {$productTable} (`entity_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_STOCK_TAX_CLASS_STOCK_ID` 
    FOREIGN KEY (`stock_id`) REFERENCES {$stockTable} (`stock_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_CATALOG_PRODUCT_STOCK_TAX_CLASS_TAX_CLASS_ID` 
    FOREIGN KEY (`tax_class_id`) REFERENCES {$taxClassTable} (`class_id`) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
