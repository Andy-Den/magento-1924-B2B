<?php

$this->startSetup();

$this->updateAttribute('customer', 'fvets_allin_status', array(
    'backend_type' => 'varchar',
    'is_required' => true,
    'default_value' => 'I'));

$this->endSetup();