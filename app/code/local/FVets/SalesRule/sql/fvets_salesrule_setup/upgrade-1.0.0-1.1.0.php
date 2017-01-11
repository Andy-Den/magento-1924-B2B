<?php
/**
 * FVets_TablePrice extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * TablePrice module install script
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
$this->startSetup();

$table = $this->getConnection()
	->newTable($this->getTable('fvets_salesrule/label'))
	->addColumn(
		'label_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned' => true,
			'identity' => true,
			'nullable' => false,
			'primary' => true,
		),
		'Label Id'
	)
	->addColumn(
		'salesrule_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned' => true,
			'nullable' => false,
			'default' => '0',
		),
		'Salesrule Id'
	)
	->addColumn(
		'product_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned' => true,
			'nullable' => false,
			'default' => '0',
		),
		'Product Id'
	)
	->addColumn(
		'short_name',
		Varien_Db_Ddl_Table::TYPE_VARCHAR,
		null,
		array(
			'nullable' => false
		),
		'Short Name'
	)
	->addIndex(
		$this->getIdxName(
			'fvets_salesrule/label',
			array('label_id')
		),
		array('label_id')
	)
	->addForeignKey(
		$this->getFkName(
			'fvets_salesrule/label',
			'salesrule_id',
			'salesrule/rule',
			'rule_id'
		),
		'salesrule_id',
		$this->getTable('salesrule/rule'),
		'rule_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE,
		Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	->addForeignKey(
		$this->getFkName(
			'fvets_salesrule/label',
			'product_id',
			'catalog/product',
			'entity_id'
		),
		'product_id',
		$this->getTable('catalog/product'),
		'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE,
		Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	->addIndex(
		$this->getIdxName(
			'fvets_salesrule/label',
			array('salesrule_id', 'product_id'),
			Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
		),
		array('salesrule_id', 'product_id'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
	)
	->setComment('Table to store promotional products labels');
$this->getConnection()->createTable($table);

$this->endSetup();