<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule module install script
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
$this->startSetup();
$this->getConnection()
	->addColumn(
		$this->getTable('salesrule'),
		'apply_to_all',
		array(
			'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
			'nullable'  => false,
			'default' => '1',
			'comment' => 'Apply to all customers'
		)
	)
;
$this->getConnection()
	->addColumn(
		$this->getTable('salesrule'),
		'rule_type',
		array(
			'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'nullable'  => false,
			'default' => '0',
			'comment' => 'Rule Type'
		)
	)
;
$this->getConnection()
	->addColumn(
		$this->getTable('salesrule'),
		'stallment_discount_amount',
		array(
			'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
			'length' => '12,4',
			'nullable'  => false,
			'default' => '0',
			'comment' => 'Stallment discount amount'
		)
	)
;
$this->getConnection()
	->addColumn(
		$this->getTable('salesrule'),
		'stop_condition_discount',
		array(
			'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'nullable'  => false,
			'default' => '0',
			'comment' => 'If stop or not payment conditions discount'
		)
	)
;

$table = $this->getConnection()
    ->newTable($this->getTable('fvets_salesrule/customer'))
    ->addColumn(
        'rel_id',
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
        'SalesRule ID'
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
        'position',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable'  => false,
            'default'   => '0',
        ),
        'Position'
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_salesrule/customer',
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
			'fvets_salesrule/customer',
			array('customer_id')
		),
		array('customer_id')
	)
    ->addForeignKey(
        $this->getFkName(
            'fvets_salesrule/customer',
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
    ->addIndex(
        $this->getIdxName(
            'fvets_salesrule/customer',
            array('salesrule_id', 'customer_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('salesrule_id', 'customer_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('SalesRule to Customer Linkage Table');
$this->getConnection()->createTable($table);

//Fix magento bug
$foreignKeys = $table->getForeignKeys();
foreach($foreignKeys as $foreignKey)
{
	if ($foreignKey['REF_TABLE_NAME'] == 'customer')
	{
		$this->run('ALTER TABLE `'.$this->getTable('fvets_salesrule/customer').'` DROP FOREIGN KEY `'.$foreignKey['FK_NAME'].'` ;');
		$this->run('ALTER TABLE `'.$this->getTable('fvets_salesrule/customer').'` ADD CONSTRAINT `'.$foreignKey['FK_NAME'].'` FOREIGN KEY ( `customer_id` ) REFERENCES `customer_entity` (
			`entity_id`
			) ON DELETE CASCADE ON UPDATE CASCADE ;');
	}
}

$table = $this->getConnection()
	->newTable($this->getTable('fvets_salesrule/premier'))
	->addColumn(
		'rel_id',
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
		'attribute_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '0',
		),
		'Attribute ID'
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
		'SalesRule ID'
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
		'group',
		Varien_Db_Ddl_Table::TYPE_VARCHAR,
		'50',
		array(
			'nullable'  => true
		),
		'Premier rules group'
	)
	->addForeignKey(
		$this->getFkName(
			'fvets_salesrule/premier',
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
			'fvets_salesrule/premier',
			array('salesrule_id'),
			Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
		),
		array('salesrule_id'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
	)
	->addIndex(
		$this->getIdxName(
			'fvets_salesrule/premier',
			array('group'),
			Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
		),
		array('group'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
	)
	->setComment('SalesRule Premier Commercial Policy Table');
$this->getConnection()->createTable($table);

$this->endSetup();
