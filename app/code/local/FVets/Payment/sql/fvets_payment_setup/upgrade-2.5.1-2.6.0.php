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
$this->run("ALTER TABLE " . $this->getTable('fvets_payment/condition') . " MODIFY COLUMN id_erp VARCHAR(255) NOT NULL COMMENT 'ID ERP';");
$this->endSetup();
