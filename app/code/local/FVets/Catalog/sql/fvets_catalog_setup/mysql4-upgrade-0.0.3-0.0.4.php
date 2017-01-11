<?php

$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
//Remove fvets_coupon attribute
$installer->removeAttribute(Mage_Catalog_Model_Category::ENTITY, 'type');
$installer->endSetup();