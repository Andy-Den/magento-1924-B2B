<?php

$this->startSetup();

$this->updateAttribute('customer', 'fvets_allin_status', array(
	'default_value' => 'V'));

$this->endSetup();