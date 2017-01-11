<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE `widgento_login` CHANGE `admin_id` `admin_id` SMALLINT( 5 ) UNSIGNED NULL DEFAULT NULL ;");

$installer->endSetup();