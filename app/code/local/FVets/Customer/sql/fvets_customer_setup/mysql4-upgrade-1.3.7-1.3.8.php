<?php

$this->startSetup();

$this->updateAttribute('customer', 'commission', array(
    'default_value' => null, 'backend_type' => 'decimal'));
$this->endSetup();