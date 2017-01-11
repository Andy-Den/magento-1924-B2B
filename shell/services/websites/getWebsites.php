<?php
/**
 * Created by PhpStorm.
 * User: fvets
 * Date: 7/26/16
 * Time: 10:13 AM
 */

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

array_shift($argv);
list($websiteId) = $argv;

if (empty($websiteId)) {
    die('O website deve ser selecionado.');
}

$website = Mage::getModel('core/website')->load($websiteId);
$arrayResult = array();
if ($website) {
    $arrayResult['status'] = 200;
    $arrayResult['website_data'] = $website->getData();
} else {
    $arrayResult['status'] = 404;
}

die(json_encode($arrayResult));
