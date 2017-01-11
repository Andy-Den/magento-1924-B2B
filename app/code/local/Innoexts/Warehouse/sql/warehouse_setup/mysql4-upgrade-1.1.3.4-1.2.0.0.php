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
$installer                                  = $this;

$connection                                 = $installer->getConnection();

$customerGroupTable                         = $installer->getTable('customer/customer_group');
$warehouseTable                             = $installer->getTable('warehouse/warehouse');
$warehouseCustomerGroupTable                = $installer->getTable('warehouse/warehouse_customer_group');
$warehouseCurrencyTable                     = $installer->getTable('warehouse/warehouse_currency');

$installer->startSetup();

/**
 * Warehouse Customer Group
 */
$installer->run("
CREATE TABLE `{$warehouseCustomerGroupTable}` (
  `warehouse_id` smallint(6) unsigned not null, 
  `customer_group_id` smallint(5) unsigned not null, 
  PRIMARY KEY  (`warehouse_id`, `customer_group_id`), 
  KEY `FK_WAREHOUSE_CUSTOMER_GROUP_WAREHOUSE_ID` (`warehouse_id`), 
  KEY `FK_WAREHOUSE_CUSTOMER_GROUP_CUSTOMER_GROUP_ID` (`customer_group_id`), 
  CONSTRAINT `FK_WAREHOUSE_CUSTOMER_GROUP_WAREHOUSE` FOREIGN KEY (`warehouse_id`) 
    REFERENCES {$warehouseTable} (`warehouse_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE, 
  CONSTRAINT `FK_WAREHOUSE_CUSTOMER_GROUP_CUSTOMER_GROUP_ID` FOREIGN KEY (`customer_group_id`) 
    REFERENCES {$customerGroupTable} (`customer_group_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Warehouse Currency
 */
$installer->run("
CREATE TABLE `{$warehouseCurrencyTable}` (
  `warehouse_id` smallint(6) unsigned not null, 
  `currency` varchar(3) not null, 
  PRIMARY KEY  (`warehouse_id`, `currency`), 
  KEY `FK_WAREHOUSE_CURRENCY_WAREHOUSE_ID` (`warehouse_id`), 
  KEY `IDX_WAREHOUSE_CURRENCY_CURRENCY` (`currency`), 
  CONSTRAINT `FK_WAREHOUSE_CURRENCY_WAREHOUSE_ID` FOREIGN KEY (`warehouse_id`) 
    REFERENCES {$warehouseTable} (`warehouse_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
