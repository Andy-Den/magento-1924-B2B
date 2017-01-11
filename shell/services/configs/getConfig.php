<?php
/**
 * Created by PhpStorm.
 * User: fvets
 * Date: 7/26/16
 * Time: 3:32 PM
 */

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

array_shift($argv);
list($scope, $scopeValue, $path) = $argv;

if (empty($scope) || empty($path)) {
    die('Todos os campos devem ser enviados para a API.');
}

//$scope = 'website';
//$scopeValue = 2;
//$path = 'allin/general/source_site_enabled';

$data = array();

$data['status'] = 404;

if ($scope == 'website') {
    $data['config_value'] = Mage::getModel('core/website')->load($scopeValue)->getConfig($path);

    if($data['config_value'] != null) {
        $data['status'] = 200;
    } else {
        $data['status'] = 404;
    }
}

if ($scope == 'storeview') {
    $data['config_value'] = Mage::getModel('core/store')->load($scopeValue)->getConfig($path);

    if($data['config_value'] != null) {
        $data['status'] = 200;
    } else {
        $data['status'] = 404;
    }
}

die(json_encode($data));
