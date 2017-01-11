<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

$this->startSetup();

$this->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('amrolepermissions/rule')}` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `scope_websites` varchar(255) NOT NULL,
  `scope_storeviews` varchar(255) NOT NULL,
  `categories` varchar(512) NOT NULL,
  `products` varchar(512) NOT NULL,
  PRIMARY KEY (`rule_id`),
  UNIQUE KEY `role_id` (`role_id`)
) ENGINE=InnoDB;

ALTER TABLE `{$this->getTable('amrolepermissions/rule')}`
  ADD FOREIGN KEY (`role_id`) REFERENCES `{$this->getTable('admin/role')}` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$this->addAttribute('catalog_product', 'amrolepermissions_owner', array(
    'group'         => 'General',
    'backend'       => '',
    'frontend'      => '',
    'class'         => '',
    'default'       => '',
    'label'         => 'Product Owner',
    'input'         => 'select',
    'type'          => 'int',
    'source'        => 'amrolepermissions/attribute_source_admins',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       => 1,
    'required'      => 0,
    'searchable'    => 0,
    'filterable'    => 0,
    'unique'        => 0,
    'comparable'    => 0,
    'visible_on_front' => 0,
    'is_configurable' => 0,
    'is_html_allowed_on_front' => 0,
    'user_defined'  => 1,
));

$this->endSetup();
