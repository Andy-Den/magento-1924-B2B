<?php

/** @var Insere permissões os blocks usados nos cms do site.  */

$installer = $this;

$installer->startSetup();

$installer->getConnection()->insertMultiple(
$installer->getTable('admin/permission_block'),
	array(
		array('block_name' => 'customer/form_login', 'is_allowed' => 1),
		array('block_name' => 'catalog/navigation', 'is_allowed' => 1),
		array('block_name' => 'page/html', 'is_allowed' => 1),
	)
);

$installer->endSetup();