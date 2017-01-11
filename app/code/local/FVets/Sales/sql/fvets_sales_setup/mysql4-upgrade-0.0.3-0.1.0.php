<?php

$this->startSetup();

$table = $this->getConnection()
	->newTable($this->getTable('fvets_sales/offline_order'))
	->addColumn(
		'entity_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'identity'  => true,
			'nullable'  => false,
			'primary'   => true,
		),
		'Entity ID'
	)
	->addColumn(
		'website_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '0',
		),
		'Website ID'
	)
	->addColumn(
		'order_id_erp',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '0',
		),
		'Order ID ERP'
	)
	->addColumn(
		'customer_id_erp',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '0',
		),
		'Customer ID ERP'
	)
	->addColumn(
		'order_date',
		Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
		null,
		array(
			'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
			'nullable'  => false
		),
		'Order Creation Time'
	)
	->addColumn(
		'total',
		Varien_Db_Ddl_Table::TYPE_DECIMAL,
		'10,2',
		array(
			'nullable'  => false,
			'default'   => '0',
		),
		'Total order value'
	)
	->addColumn(
		'premier_salesrule_processed',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		'1',
		array(
			'nullable'  => false,
			'default'   => '0',
		),
		'If is premier salesrule processed information'
	)
	->addForeignKey(
		$this->getFkName(
			'fvets_sales_offline_order',
			'website_id',
			'core/website',
			'website_id'
		),
		'website_id',
		$this->getTable('core/website'),
		'website_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE,
		Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	->addIndex(
		$this->getIdxName(
			'fvets_sales/offline_order',
			array('website_id'),
			Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
		),
		array('website_id'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
	)
	->addIndex(
		$this->getIdxName(
			'fvets_sales/offline_order',
			array('order_id_erp'),
			Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
		),
		array('order_id_erp'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
	)
	->addIndex(
		$this->getIdxName(
			'fvets_sales/offline_order',
			array('customer_id_erp')
		),
		array('customer_id_erp'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
	)
	->setComment('Offline order storage table');
$this->getConnection()->createTable($table);



$table = $this->getConnection()
	->newTable($this->getTable('fvets_sales/offline_order_item'))
	->addColumn(
		'entity_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'identity'  => true,
			'nullable'  => false,
			'primary'   => true,
		),
		'Entity ID'
	)
	->addColumn(
		'offline_order_id',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '0',
		),
		'Offline Order ID'
	)
	->addColumn(
		'item_id_erp',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '0',
		),
		'Item ID ERP'
	)
	->addColumn(
		'qty',
		Varien_Db_Ddl_Table::TYPE_INTEGER,
		null,
		array(
			'nullable'  => false,
			'default'   => '0',
		),
		'Total item qty'
	)
	->addForeignKey(
		$this->getFkName(
			'fvets_sales_offline_order_item',
			'offline_order_id',
			'fvets_sales/offline_order',
			'entity_id'
		),
		'offline_order_id',
		$this->getTable('fvets_sales/offline_order'),
		'entity_id',
		Varien_Db_Ddl_Table::ACTION_CASCADE,
		Varien_Db_Ddl_Table::ACTION_CASCADE
	)
	->addIndex(
		$this->getIdxName(
			'fvets_sales/offline_order_item',
			array('offline_order_id'),
			Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
		),
		array('offline_order_id'),
		array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
	)
	->setComment('Offline order item storage table');
$this->getConnection()->createTable($table);

$this->endSetup();