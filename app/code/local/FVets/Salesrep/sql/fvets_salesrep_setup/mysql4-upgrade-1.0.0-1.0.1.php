<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn(
		$installer->getTable('fvets_salesrep/salesrep'), 'id_erp', 'varchar(10)', null, array('default' => 'null')
);

$installer->endSetup();
