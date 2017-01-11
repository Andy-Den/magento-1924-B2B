<?php

$this->startSetup();

$this->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('fvets_allin/account')}` (
    id integer unsigned not null auto_increment primary key,
    name varchar(255) not null,
    website_id smallint(5) unsigned not null,
    user varchar(255) null,
    password varchar(255) null,
    list_name varchar(255) null,
    CONSTRAINT FOREIGN KEY (website_id) REFERENCES `{$this->getTable('core/website')}` (website_id),
    UNIQUE INDEX `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
");

$accountTable = $this->getTable('fvets_allin/account');
$synchronizeTable = $this->getTable('fvets_allin/synchronize');
$synchronizeAccountFk = 'fk_' . $this->getTable('fvets_allin/synchronize') . '_' . $this->getTable('fvets_allin/account');

$this->run("
CREATE TABLE IF NOT EXISTS `{$synchronizeTable}` (
	id integer unsigned not null auto_increment primary key,
	`id_account` INT(10) UNSIGNED NOT NULL,
	`data` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	INDEX `{$synchronizeAccountFk}` (`id_account`),
	CONSTRAINT `{$synchronizeAccountFk}` FOREIGN KEY (`id_account`) REFERENCES `{$accountTable}` (`id`)
)
ENGINE=InnoDB;
");

$this->endSetup();
