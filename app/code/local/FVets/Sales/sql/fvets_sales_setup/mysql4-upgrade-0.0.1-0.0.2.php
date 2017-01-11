<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
	$installer->getTable('sales_flat_order'), 'id_erp', 'varchar(50)'
);

$installer->endSetup();

