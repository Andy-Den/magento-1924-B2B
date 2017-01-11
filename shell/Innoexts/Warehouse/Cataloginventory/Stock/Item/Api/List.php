<?php

# API DOMAIN
$apiDomain              = 'magento7.localhost';
# API URL
$apiUrl                 = 'http://'.$apiDomain.'/api/soap/?wsdl';
# API Username
$apiUsername            = 'demo';
# API Key
$apiPassword            = 'john.doe';
# Product SKUs
$productSkus            = array('hde004', 'hde005');
# Stock ID
$stockId                = 4;

$soapClient             = new SoapClient($apiUrl);
$sessionId              = $soapClient->login($apiUsername, $apiPassword);
$responce               = $soapClient->call(
    $sessionId, 
    'product_stock.listByStock', 
    array($productSkus, $stockId)
);

print_r($responce);

/**
php shell/Innoexts/Warehouse/Cataloginventory/Stock/Item/Api/List.php
 */