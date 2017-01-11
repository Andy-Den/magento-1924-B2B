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

$this->run('ALTER TABLE `sales_flat_order` ADD `tableprice_discount_amount` DECIMAL(11,2) NULL ;');
$this->run('ALTER TABLE `sales_flat_quote` ADD `tableprice_discount_amount` DECIMAL(11,2) NULL ;');

$this->endSetup();