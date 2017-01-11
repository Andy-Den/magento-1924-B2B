<?php

$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('sales/quote_payment')}`
        ADD `magefm_moip_token` CHAR(60) NULL DEFAULT NULL;

    ALTER TABLE `{$this->getTable('sales/quote_payment')}`
        ADD `magefm_moip_result` TEXT NULL DEFAULT NULL;

    ALTER TABLE `{$this->getTable('sales/order_payment')}`
        ADD `magefm_moip_token` CHAR(60) NULL DEFAULT NULL;
    
    ALTER TABLE `{$this->getTable('sales/order_payment')}`
        ADD `magefm_moip_result` TEXT NULL DEFAULT NULL;
");

$this->endSetup();
