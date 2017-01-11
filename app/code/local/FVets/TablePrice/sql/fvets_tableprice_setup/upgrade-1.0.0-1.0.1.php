<?php
/**
 * FVets_TablePrice extension
 *
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 */
/**
 * TablePrice module install script
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
$this->startSetup();

$this->getConnection()
	->addColumn(
		$this->getTable('fvets_tableprice'),
		'discount',
		array(
			'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
			'length' => '11,2',
			'nullable'  => false,
			'default' => '0',
			'comment' => 'Tableprice discount'
		)
	);

$this->endSetup();