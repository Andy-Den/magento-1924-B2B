<?php
/**
 * @copyright Amasty.
 */
$this->startSetup();

$fieldsSql = 'SHOW COLUMNS FROM ' . $this->getTable('salesrule/rule');
$cols = $this->getConnection()->fetchCol($fieldsSql);

if (!in_array('first_sale_only', $cols))
{
	$this->run("ALTER TABLE `{$this->getTable('salesrule/rule')}` ADD `first_sale_only` SMALLINT(6) NOT NULL DEFAULT 0");
}

$this->endSetup();