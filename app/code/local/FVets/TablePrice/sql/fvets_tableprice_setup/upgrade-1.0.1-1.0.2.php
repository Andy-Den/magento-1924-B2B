<?php
/**
 * FVets_TablePrice extension
 *
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 */
/**
 * TablePrice module install script
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
$this->startSetup();

$this->run('ALTER TABLE `sales_flat_order_item` ADD `tableprice_discount_percent` INT(11) NULL ;');
$this->run('ALTER TABLE `sales_flat_quote_item` ADD `tableprice_discount_percent` INT(11) NULL ;');

$this->run('ALTER TABLE `sales_flat_order_item` ADD `tableprice_discount_amount` DECIMAL(11,2) NULL ;');
$this->run('ALTER TABLE `sales_flat_quote_item` ADD `tableprice_discount_amount` DECIMAL(11,2) NULL ;');

$this->endSetup();