<?php
/**
 * Classic_Distributor extension
 *
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Distributor module install script
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
$this->startSetup();
$table = $this->getConnection()
    ->newTable($this->getTable('fvets_salesrep/region'))
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
        'Distributor ID'
    )
    ->addColumn(
        'region_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
        ),
        'Region ID'
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
            'fvets_salesrep/region',
            array('region_id')
        ),
        array('region_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'fvets_salesrep/region',
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
            'fvets_salesrep/region',
            'region_id',
			'directory/country_region',
            'region_id'
        ),
        'region_id',
        $this->getTable('directory/country_region'),
        'region_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            'fvets_salesrep/region',
            array('salesrep_id', 'region_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('salesrep_id', 'region_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Salesrep to Region Linkage Table');
$this->getConnection()->createTable($table);
$this->endSetup();
