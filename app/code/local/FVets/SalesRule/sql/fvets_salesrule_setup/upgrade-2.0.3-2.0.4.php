<?php

$this->startSetup();

$table = $this->getConnection()
	->newTable($this->getTable('fvets_salesrule/premier_report'))
	->addColumn(
		'report_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'identity'  => true,
			'nullable'  => false,
			'primary'   => true,
		),
		'Relation ID'
	)
	->addColumn(
		'salesrule_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '0',
		),
		'Salesrule ID'
	)
	->addColumn(
		'customer_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '0',
		),
		'Customer ID'
	)
	->addColumn(
		'group',
		Varien_Db_Ddl_Table::TYPE_VARCHAR,
		'50',
		array(
			'nullable'  => true
		),
		'Premier rules group'
	)
	->addColumn(
		'from',
		Varien_Db_Ddl_Table::TYPE_DECIMAL,
		'10,2',
		array(
			'nullable'  => false,
			'default'   => '0',
		),
		'From'
	)
	->addColumn(
		'to',
		Varien_Db_Ddl_Table::TYPE_DECIMAL,
		'10,2',
		array(
			'nullable'  => false,
			'default'   => '0',
		),
		'To'
	)
	->addColumn(
		'modified_by',
		Varien_Db_Ddl_Table::TYPE_VARCHAR,
		'50',
		array(
			'nullable'  => false
		),
		'Ho modified customer'
	)
	->addColumn(
		'salesrep_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'nullable'  => true,
			'default'   => null,
		),
		'Salesrep ID'
	)
	->addColumn(
		'created_at',
		Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
		null,
		array(
			'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
		),
		'Report Creation Time'
	)
	->addForeignKey(
		$this->getFkName(
			'fvets_salesrule/premier_report',
			'salesrule_id',
			'salesrule',
			'rule_id'
		),
		'salesrule_id',
		$this->getTable('salesrule'),
		'rule_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE,
		Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	->addIndex(
		$this->getIdxName(
			'fvets_salesrule/premier_report',
			array('salesrule_id'),
			Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
		),
		array('salesrule_id'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
	)
	->addForeignKey(
		$this->getFkName(
			'fvets_salesrule/premier_report',
			'salesrep_id',
			'fvets_salesrep',
			'id'
		),
		'salesrep_id',
		$this->getTable('fvets_salesrep'),
		'id',
		Varien_Db_Ddl_Table::ACTION_CASCADE,
		Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	->addIndex(
		$this->getIdxName(
			'fvets_salesrule/premier_report',
			array('salesrep_id'),
			Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
		),
		array('salesrep_id'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
	)
	->addIndex(
		$this->getIdxName(
			'fvets_salesrule/premier_report',
			array('customer_id')
		),
		array('customer_id')
	)
	->addForeignKey(
		$this->getFkName(
			'fvets_salesrule/premier_report',
			'customer_id',
			'customer',
			'entity_id'
		),
		'customer_id',
		$this->getTable('customer'),
		'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE,
		Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	->setComment('SalesRule Premier Commercial Policy Report Table');
$this->getConnection()->createTable($table);

//Fix magento bug
$foreignKeys = $table->getForeignKeys();
foreach($foreignKeys as $foreignKey)
{
	if ($foreignKey['REF_TABLE_NAME'] == 'customer')
	{
		$this->run('ALTER TABLE `'.$this->getTable('fvets_salesrule/premier_report').'` DROP FOREIGN KEY `'.$foreignKey['FK_NAME'].'` ;');
		$this->run('ALTER TABLE `'.$this->getTable('fvets_salesrule/premier_report').'` ADD CONSTRAINT `'.$foreignKey['FK_NAME'].'` FOREIGN KEY ( `customer_id` ) REFERENCES `customer_entity` (
			`entity_id`
			) ON DELETE CASCADE ON UPDATE CASCADE ;');
	}
}

$this->endSetup();