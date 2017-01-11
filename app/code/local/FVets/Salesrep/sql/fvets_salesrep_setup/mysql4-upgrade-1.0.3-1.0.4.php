<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn(
	$installer->getTable('fvets_salesrep/salesrep'), 'brands', 'varchar(255)', null, array('default' => 'null')
);

$installer->endSetup();