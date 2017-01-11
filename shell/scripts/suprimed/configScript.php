<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/6/15
 * Time: 11:34 AM
 */

require_once '../../../app/Mage.php';
umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$websiteId = 18;