<?php
$this->startSetup();

$this->run("ALTER TABLE `{$this->getTable('fvets_salesrep/salesrep')}` DROP FOREIGN KEY `fvets_salesrep_ibfk_1` ;");
$this->run("ALTER TABLE `{$this->getTable('fvets_salesrep/salesrep')}` DROP INDEX website_id;");
$this->run("ALTER TABLE `{$this->getTable('fvets_salesrep/salesrep')}` CHANGE `website_id` `store_id` VARCHAR( 20 ) NOT NULL ;");
$this->run("ALTER TABLE `{$this->getTable('fvets_salesrep/salesrep')}` ADD INDEX ( `store_id` ) ;");

$this->run("UPDATE `{$this->getTable('fvets_salesrep/salesrep')}` SET `{$this->getTable('fvets_salesrep/salesrep')}`.`store_id` = ( SELECT GROUP_CONCAT(`{$this->getTable('core/store')}`.`store_id` SEPARATOR ',')
FROM `{$this->getTable('core/store')}`
WHERE `{$this->getTable('core/store')}`.`website_id` = `{$this->getTable('fvets_salesrep/salesrep')}`.`store_id` ) ");

$this->endSetup();