<?php
/**
 * FVets_Salesrep extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Salesrep module install script
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
$this->startSetup();

//Add updated_at and created_at column column

$this->getConnection()
	->addColumn(
		$this->getTable('fvets_salesrep'), 'status', 'SMALLINT NOT NULL');
$this->getConnection()
	->addColumn(
		$this->getTable('fvets_salesrep'), 'updated_at', 'TIMESTAMP NOT NULL');
$this->getConnection()
	->addColumn(
		$this->getTable('fvets_salesrep'), 'created_at', 'TIMESTAMP NOT NULL');

/*$this->run('
	ALTER TABLE `'.$this->getTable('fvets_salesrep').'` ADD `status` SMALLINT NOT NULL ,
	ADD `updated_at` TIMESTAMP NOT NULL ,
	ADD `created_at` TIMESTAMP NOT NULL ;
');*/

//Remove brands column
$this->run('
	ALTER TABLE `'.$this->getTable('fvets_salesrep').'` DROP `brands`;
');


$table = $this->getConnection()
    ->newTable($this->getTable('fvets_salesrep/category'))
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
        'salesrep_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Sales Rep ID'
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
            'fvets_salesrep/category',
            array('category_id')
        ),
        array('category_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_salesrep/category',
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
    ->addForeignKey(
        $this->getFkName(
            'fvets_salesrep/category',
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
            'fvets_salesrep/category',
            array('salesrep_id', 'category_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('salesrep_id', 'category_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Sales Rep to Category Linkage Table');
$this->getConnection()->createTable($table);


//Set status enabled to all salesrep
$this->run('
	UPDATE `'.$this->getTable('fvets_salesrep').'` SET `status` = \'1\';
');



//Add salesrep column to quote_item
/*$this->getConnection()
	->addColumn(
		$this->getTable('sales/quote_item'), 'salesrep_id', 'INT(10) UNSIGNED NULL');
$this->getConnection()
	->addColumn(
		$this->getTable('sales/order_item'), 'salesrep_id', 'INT(10) UNSIGNED NULL');

$this->run(
	'ALTER TABLE `'.$this->getTable('sales/quote_item').'` ADD INDEX ( `salesrep_id` ) ;'
);

$this->run(
	'ALTER TABLE `'.$this->getTable('sales/order_item').'` ADD INDEX ( `salesrep_id` ) ;'
);*/

$this->endSetup();
