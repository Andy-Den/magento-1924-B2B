<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
$installer->removeAttribute('customer_address', 'identification');
$installer->endSetup();