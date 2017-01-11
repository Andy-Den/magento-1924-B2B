<?php

$this->startSetup();

$this->updateAttribute('customer', 'origem', array(
	'default_value' => null));

$this->endSetup();