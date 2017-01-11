<?php

$this->startSetup();

$trashAccountFk = 'fk_' . $this->getTable('fvets_allin/trash') . '_' . $this->getTable('fvets_allin/account');
$accountTable = $this->getTable('fvets_allin/account');

$this->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('fvets_allin/trash')}` (
    id integer unsigned not null auto_increment primary key,
    id_account integer unsigned not null,
    email varchar(255) not null,
    status integer not null default 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `{$trashAccountFk}` FOREIGN KEY (`id_account`) REFERENCES `{$accountTable}` (`id`),
    unique key (id_account, email, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
");

$this->endSetup();
