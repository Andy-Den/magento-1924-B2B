<?php

$this->startSetup();
$this->getConnection()->modifyColumn($this->getTable('classic_distributor'), 'website', "INT(11) NULL DEFAULT NULL");
$this->endSetup();
