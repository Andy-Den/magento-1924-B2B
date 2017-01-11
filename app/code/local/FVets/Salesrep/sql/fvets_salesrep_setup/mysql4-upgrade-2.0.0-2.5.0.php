<?php

$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$installer->addAttribute(
	'order_item',
	'salesrep_id',
	array(
		'type' => 'int' /* varchar, text, decimal, datetime */,
		'grid' => true /* or true if you wan't use this attribute on orders grid page */
	)
);
$installer->addAttribute(
	'quote_item',
	'salesrep_id',
	array(
		'type' => 'int' /* varchar, text, decimal, datetime */,
		'grid' => true /* or true if you wan't use this attribute on orders grid page */
	)
);


$installer
	->getConnection()
	->addConstraint(
		'FK_SALES_ORDER_ITEM_SALESREP',
		$installer->getTable('sales/order_item'),
		'salesrep_id',
		$installer->getTable('fvets_salesrep'),
		'id',
		'set null',
		'cascade'
	);

$installer
	->getConnection()
	->addConstraint(
		'FK_SALES_QUOTE_ITEM_SALESREP',
		$installer->getTable('sales/quote_item'),
		'salesrep_id',
		$installer->getTable('fvets_salesrep'),
		'id',
		'set null',
		'cascade'
	);

$installer->endSetup();

