<?php

$this->startSetup();

$this->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('fvets_salesrep/salesrep')}` (
    id integer unsigned not null auto_increment primary key,
    website_id smallint(5) unsigned not null,
    name varchar(255) not null,
    email varchar(255) null,
    telephone varchar(255) null,
    CONSTRAINT FOREIGN KEY (website_id) REFERENCES `{$this->getTable('core/website')}` (website_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
");

$entityTypeId = $this->getEntityTypeId('customer');

$this->addAttribute($entityTypeId, 'fvets_salesrep', array(
    'input' => 'select',
    'type' => 'int',
    'label' => 'Sales Representative',
    'source' => 'fvets_salesrep/source_salesrep',
    'required' => false,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible' => true,
));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'fvets_salesrep');
$attribute->setData('used_in_forms', array('adminhtml_customer'));
$attribute->save();

$this->endSetup();
