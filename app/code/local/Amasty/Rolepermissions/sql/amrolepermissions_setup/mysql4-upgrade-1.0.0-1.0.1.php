<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('amrolepermissions/rule')}` ADD `limit_orders` BOOLEAN NOT NULL;
");

$this->endSetup();
