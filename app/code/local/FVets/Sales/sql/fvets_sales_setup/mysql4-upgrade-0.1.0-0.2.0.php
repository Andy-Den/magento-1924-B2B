<?php

$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$installer->addAttribute(
    'order_item',
    'option_packing',
    array(
        'type' => 'varchar' /* varchar, text, decimal, datetime */,
        'grid' => false /* or true if you wan't use this attribute on orders grid page */
    )
);
$installer->addAttribute(
    'quote_item',
    'option_packing',
    array(
        'type' => 'varchar' ,
        'grid' => true
    )
);

$installer->endSetup();

