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

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

$websitesId = array(2, 4, 5);