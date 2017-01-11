<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
	$installer->getTable('sales_flat_order'), 'salesrep_id', 'int(10)'
);

$installer->endSetup();

