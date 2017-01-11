<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
	$installer->getTable('sales_flat_order'), 'exported', 'char(1)'
);

$installer->endSetup();

