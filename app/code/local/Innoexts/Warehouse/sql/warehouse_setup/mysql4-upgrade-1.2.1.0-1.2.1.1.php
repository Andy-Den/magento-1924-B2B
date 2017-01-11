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
$adapter                                        = $installer->getConnection();

$warehouseTable                                 = $installer->getTable('warehouse/warehouse');

$installer->startSetup();

/**
 * Warehouse
 */
$adapter->addColumn(
    $warehouseTable, 
    'origin_street1', 
    "varchar(255) not null after `origin_city`"
);
$adapter->addColumn(
    $warehouseTable, 
    'origin_street2', 
    "varchar(255) not null default '' after `origin_street1`"
);

$installer->endSetup();
