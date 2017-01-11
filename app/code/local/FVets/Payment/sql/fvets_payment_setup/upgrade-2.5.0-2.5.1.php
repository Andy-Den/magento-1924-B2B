<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Payment module upgrade script
 *
 * @category    FVets
 * @package     FVets_Payment
 */
$this->startSetup();

$this->run("ALTER TABLE sales_flat_order ADD payment_increase decimal(16,8) DEFAULT 0 NULL AFTER payment_discount;");
$this->run("ALTER TABLE sales_flat_order ADD payment_increase_amount decimal(16,8) DEFAULT 0 NULL AFTER payment_increase;");

$this->run("ALTER TABLE sales_flat_quote ADD payment_increase decimal(16,8) DEFAULT 0 NULL AFTER payment_discount;");
$this->run("ALTER TABLE sales_flat_quote ADD payment_increase_amount decimal(16,8) DEFAULT 0 NULL AFTER payment_increase;");

$this->run("ALTER TABLE sales_flat_order_item ADD increase_percent decimal(16,8) DEFAULT 0 NULL AFTER base_discount_amount;");
$this->run("ALTER TABLE sales_flat_order_item ADD increase_amount decimal(16,8) DEFAULT 0 NULL AFTER increase_percent;");

$this->run("ALTER TABLE sales_flat_quote_item ADD increase_percent decimal(16,8) DEFAULT 0 NULL AFTER base_discount_amount;");
$this->run("ALTER TABLE sales_flat_quote_item ADD increase_amount decimal(16,8) DEFAULT 0 NULL AFTER increase_percent;");

$this->endSetup();
