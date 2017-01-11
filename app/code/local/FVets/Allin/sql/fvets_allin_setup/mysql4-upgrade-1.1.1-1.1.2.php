<?php

$this->startSetup();

$this->updateAttribute('customer', 'block_allin_status', array(
	'default_value' => 0));

$this->endSetup();