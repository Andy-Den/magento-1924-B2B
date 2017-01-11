<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/6/15
 * Time: 11:34 AM
 */

require_once '../../../app/Mage.php';
umask(0);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

Mage::app()->setCurrentStore(0);

$resource = Mage::getModel('core/resource');
$writeConnection = $resource->getConnection('core_write');
$readConnection = $resource->getConnection('core_read');

$websiteId = 1;