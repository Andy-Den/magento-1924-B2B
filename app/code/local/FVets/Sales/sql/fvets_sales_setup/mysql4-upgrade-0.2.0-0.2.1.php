<?php

$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$installer->addAttribute(
    'order_item',
    'real_original_price',
    array(
        'type' => 'decimal' /* varchar, text, decimal, datetime */,
        'grid' => false /* or true if you wan't use this attribute on orders grid page */
    )
);

$installer->endSetup();

