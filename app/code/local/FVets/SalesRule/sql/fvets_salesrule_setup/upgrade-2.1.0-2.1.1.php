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
		'is_mix',
		array(
			'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
			'nullable'  => false,
			'default' => '0',
			'comment' => 'Is a mix rule?'
		)
	)
;
$this->endSetup();
