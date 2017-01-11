<?php

$this->startSetup();

$this->updateAttribute('customer', 'status_secondary_email', array(
	'default_value' => null));

$this->endSetup();