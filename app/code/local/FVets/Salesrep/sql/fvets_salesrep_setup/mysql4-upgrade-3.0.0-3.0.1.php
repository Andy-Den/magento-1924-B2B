<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn(
	$installer->getTable('fvets_salesrep/salesrep'), 'comission', 'decimal(16,8)', null, array('default' => 'null')
);

$installer->endSetup();