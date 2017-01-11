<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * CatalogRestrictionGroup module install script
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
$this->startSetup();
$table = $this->getConnection()
    ->newTable($this->getTable('fvets_catalogrestrictiongroup/entity'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Restriction Group ID'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Name'
    )
    ->addColumn(
        'website_id',
        Varien_Db_Ddl_Table::TYPE_TEXT, null,
        array(
            'nullable'  => false,
        ),
        'Website'
    )
    ->addColumn(
        'because',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(
            'nullable'  => false,
        ),
        'Because'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(),
        'Enabled'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Restriction Group Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Restriction Group Creation Time'
    ) 
    ->setComment('Restriction Group Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('fvets_catalogrestrictiongroup/entity_product'))
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
        'catalogrestrictiongroup_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Restriction Group ID'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Product ID'
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
    ->addIndex(
        $this->getIdxName(
            'fvets_catalogrestrictiongroup/entity_product',
            array('product_id')
        ),
        array('product_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_catalogrestrictiongroup/entity_product',
            'catalogrestrictiongroup_id',
            'fvets_catalogrestrictiongroup/entity',
            'entity_id'
        ),
        'catalogrestrictiongroup_id',
        $this->getTable('fvets_catalogrestrictiongroup/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_catalogrestrictiongroup/entity_product',
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
            'fvets_catalogrestrictiongroup/entity_product',
            array('catalogrestrictiongroup_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('catalogrestrictiongroup_id', 'product_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Restriction Group to Product Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('fvets_catalogrestrictiongroup/entity_customer'))
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
        'catalogrestrictiongroup_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Restriction Group ID'
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
    ->addIndex(
        $this->getIdxName(
            'fvets_catalogrestrictiongroup/entity_customer',
            array('customer_id')
        ),
        array('customer_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_catalogrestrictiongroup/entity_customer',
            'catalogrestrictiongroup_id',
            'fvets_catalogrestrictiongroup/entity',
            'entity_id'
        ),
        'catalogrestrictiongroup_id',
        $this->getTable('fvets_catalogrestrictiongroup/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_catalogrestrictiongroup/entity_customer',
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
            'fvets_catalogrestrictiongroup/entity_customer',
            array('catalogrestrictiongroup_id', 'customer_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('catalogrestrictiongroup_id', 'customer_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Restriction Group to Customer Linkage Table');
$this->getConnection()->createTable($table);

//Fix magento bug
$foreignKeys = $table->getForeignKeys();
foreach($foreignKeys as $foreignKey)
{
	if ($foreignKey['REF_TABLE_NAME'] == 'customer')
	{
		$this->run('ALTER TABLE `'.$this->getTable('fvets_catalogrestrictiongroup/entity_customer').'` DROP FOREIGN KEY `'.$foreignKey['FK_NAME'].'` ;');
		$this->run('ALTER TABLE `'.$this->getTable('fvets_catalogrestrictiongroup/entity_customer').'` ADD CONSTRAINT `'.$foreignKey['FK_NAME'].'` FOREIGN KEY ( `customer_id` ) REFERENCES `customer_entity` (
			`entity_id`
			) ON DELETE CASCADE ON UPDATE CASCADE ;');
	}
}$this->endSetup();
