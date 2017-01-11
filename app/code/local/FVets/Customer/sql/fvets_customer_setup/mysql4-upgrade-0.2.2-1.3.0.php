<?php
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE `customer_group` ADD `website_id` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT '0', ADD INDEX ( `website_id` ) ");
$installer->run('ALTER TABLE `customer_group` ADD CONSTRAINT `FK_CUSTOMER_GROUP_WEBSITE_ID_CORE_WEBSITE_WEBSITE_ID` FOREIGN KEY ( `website_id` ) REFERENCES `core_website` (`website_id`) ON DELETE RESTRICT ON UPDATE CASCADE ;');

$installer->endSetup();