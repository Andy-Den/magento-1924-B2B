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

$connection                                     = $installer->getConnection();

$helper                                         = Mage::helper('warehouse');
$databaseHelper                                 = $helper->getCoreHelper()->getDatabaseHelper();

$shippingTablerateTableName                     = 'shippingtablerate/tablerate';
$shippingTablerateTable                         = $installer->getTable($shippingTablerateTableName);
$shippingTablerateMethodTable                   = $installer->getTable('shippingtablerate/tablerate_method');

$installer->startSetup();

/**
 * Shipping Tablerate Method
 */
$installer->run("
CREATE TABLE `{$shippingTablerateMethodTable}` (
  `method_id` smallint(5) unsigned not null auto_increment, 
  `code` varchar(32) not null default '', 
  `name` varchar(128) default NULL, 
  PRIMARY KEY  (`method_id`), 
  KEY `IDX_SHIPPING_TABLERATE_METHOD_CODE` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Shipping Tablerate
 */
$connection->addColumn($shippingTablerateTable, 'method_id', 'smallint(5) unsigned null default null');
$connection->addConstraint(
    'FK_SHIPPING_TABLERATE_METHOD_ID', 
    $shippingTablerateTable, 
    'method_id', 
    $shippingTablerateMethodTable, 
    'method_id'
);

$databaseHelper->replaceUniqueKey(
    $installer, $shippingTablerateTableName, 'dest_country', array(
        'website_id', 
        'dest_country_id', 
        'dest_region_id', 
        'dest_zip', 
        'condition_name', 
        'condition_value', 
        'warehouse_id', 
        'method_id', 
    )
);

/**
 * Fixtures
 */

$installer->run("INSERT INTO `{$shippingTablerateMethodTable}` (`method_id`, `code`, `name`) VALUES (
    '1', {$connection->quote('default')}, {$connection->quote('Default')}
);");
    
$installer->run("UPDATE `{$shippingTablerateTable}` SET `method_id` = '1';");

$installer->endSetup();
