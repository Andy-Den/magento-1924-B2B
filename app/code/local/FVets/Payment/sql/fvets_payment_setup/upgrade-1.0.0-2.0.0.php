<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Payment module install script
 *
 * @category    FVets
 * @package     FVets_Payment
 */
$this->startSetup();
$table = $this->getConnection()
    ->newTable($this->getTable('fvets_payment/condition'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Condition ID'
    )
    ->addColumn(
        'id_erp',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
            'unsigned'  => true,
        ),
        'ID ERP'
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
        'start_days',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
            'unsigned'  => true,
        ),
        'Start Date'
    )
    ->addColumn(
        'split',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
            'unsigned'  => true,
        ),
        'Split'
    )
    ->addColumn(
        'split_range',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
            'unsigned'  => true,
        ),
        'Split Range'
    )
    ->addColumn(
        'price_range_begin',
        Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable'  => false,
        ),
        'Price Range Begin'
    )
    ->addColumn(
        'price_range_end',
        Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable'  => false,
        ),
        'Price Range End'
    )
    ->addColumn(
        'payment_methods',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(
            'nullable'  => false,
        ),
        'Payment Methods'
    )
    ->addColumn(
        'apply_to_all',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(
            'nullable'  => false,
        ),
        'Apply to all costumers'
    )
    ->addColumn(
        'apply_to_groups',
        Varien_Db_Ddl_Table::TYPE_TEXT, '64k',
        array(
            'nullable'  => false,
        ),
        'Apply to Groups'
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
        'Condition Modification Time'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Condition Creation Time'
    ) 
    ->setComment('Condition Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('fvets_payment/condition_store'))
    ->addColumn(
        'condition_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'nullable'  => false,
            'primary'   => true,
        ),
        'Condition ID'
    )
    ->addColumn(
        'store_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Store ID'
    )
    ->addIndex(
        $this->getIdxName(
            'fvets_payment/condition_store',
            array('store_id')
        ),
        array('store_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_payment/condition_store',
            'condition_id',
            'fvets_payment/condition',
            'entity_id'
        ),
        'condition_id',
        $this->getTable('fvets_payment/condition'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_payment/condition_store',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Conditions To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
    ->newTable($this->getTable('fvets_payment/condition_customer'))
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
        'condition_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Condition ID'
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
            'fvets_payment/condition_customer',
            array('customer_id')
        ),
        array('customer_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_payment/condition_customer',
            'condition_id',
            'fvets_payment/condition',
            'entity_id'
        ),
        'condition_id',
        $this->getTable('fvets_payment/condition'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_payment/condition_customer',
            'customer_id',
            'customer',
            'entity_id'
        ),
        'customer_id',
        $this->getTable('customer_entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            'fvets_payment/condition_customer',
            array('condition_id', 'customer_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('condition_id', 'customer_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Condition to Customer Linkage Table');

/** Add Customer attribute */
$this->getConnection()->createTable($table);
/*$this->addAttribute(
    'customer',
    'payment_conditions',
    array(
        'backend'           => '',
        'frontend'          => '',
        'class'             => '',
        'default'           => '',
        'label'             => 'Condition',
        'input'             => 'select',
        'type'              => 'int',
        'source'            => 'fvets_payment/condition_source',
        'global'            => false,
        'is_visible'        => 1,
        'required'          => 0,
        'searchable'        => 0,
        'filterable'        => 0,
        'unique'            => 0,
        'comparable'        => 0,
        'visible_on_front'  => 0,
        'user_defined'      => 1,
    )
);*/


$this->run("ALTER TABLE `allpago_payment_orders` ADD `payment_condition` INT( 11 ) NULL DEFAULT NULL AFTER `type` ;");
$this->run("ALTER TABLE `allpago_payment_orders` ADD INDEX ( `payment_condition` ) ;");
$this->run("ALTER TABLE `allpago_payment_orders` ADD CONSTRAINT `FK_ALLPAGO_PAYMENT_ORDERS_FVETS_PAYMENT_CONDITION_ENTITY_ID` FOREIGN KEY ( `payment_condition` ) REFERENCES `fvets_payment_condition` (`entity_id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;");

$this->endSetup();
