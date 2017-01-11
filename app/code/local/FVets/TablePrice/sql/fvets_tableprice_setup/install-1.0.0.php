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
    ->newTable($this->getTable('fvets_tableprice'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Table Price ID'
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
        'customer_group_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
        ),
        'Customer Group ID'
    )
    ->addColumn(
        'id_erp',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'ID ERP'
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
        'Table Price Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Table Price Creation Time'
    ) 
    ->setComment('Table Price Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('fvets_tableprice/category'))
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
        'tableprice_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Table Price ID'
    )
    ->addColumn(
        'category_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Category ID'
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
            'fvets_tableprice/category',
            array('category_id')
        ),
        array('category_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_tableprice/category',
            'tableprice_id',
            'fvets_tableprice',
            'entity_id'
        ),
        'tableprice_id',
        $this->getTable('fvets_tableprice'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_tableprice/category',
            'category_id',
            'catalog/category',
            'entity_id'
        ),
        'category_id',
        $this->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            'fvets_tableprice/category',
            array('tableprice_id', 'category_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('tableprice_id', 'category_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Table Price to Category Linkage Table');
$this->getConnection()->createTable($table);


$this->run('ALTER TABLE `sales_flat_order_item` ADD `tableprice` VARCHAR(50) NULL ;');
$this->run('ALTER TABLE `sales_flat_quote_item` ADD `tableprice` VARCHAR(50) NULL ;');


/*$this
	->getConnection()
	->addConstraint(
		'FK_SALES_ORDER_ITEM_TABLEPRICE',
		$this->getTable('sales/order_item'),
		'tableprice_id',
		$this->getTable('fvets_tableprice'),
		'entity_id',
		'set null',
		'cascade'
	);*/

/*$this
	->getConnection()
	->addConstraint(
		'FK_SALES_QUOTE_ITEM_TABLEPRICE',
		$this->getTable('sales/quote_item'),
		'tableprice_id',
		$this->getTable('fvets_tableprice'),
		'entity_id',
		'set null',
		'cascade'
	);*/



//Adicionado o atributo de multiplas tabelas no grupo de usuários
$this->run("ALTER TABLE `customer_group` ADD `multiple_table` INT UNSIGNED NULL  DEFAULT '0';");

$this->endSetup();