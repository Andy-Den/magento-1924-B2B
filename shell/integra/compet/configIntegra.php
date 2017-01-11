<?php
$pathBase = '/wwwroot/current/';
require_once $pathBase . 'app/Mage.php';
umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);
umask(0);

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

$websiteId = 16; // Define o Website ID da distribuidora