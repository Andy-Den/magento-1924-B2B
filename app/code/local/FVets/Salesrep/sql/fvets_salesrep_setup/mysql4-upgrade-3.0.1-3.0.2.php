<?php

$installer = $this;

$installer->startSetup();

$installer->run(
	'ALTER TABLE `'.$installer->getTable('sales_flat_order').'` ADD `salesrep_email` BOOLEAN NOT NULL DEFAULT FALSE ;'
);

$installer->endSetup();