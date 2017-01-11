<?php

# API DOMAIN
$apiDomain              = 'magento7.localhost';
# API URL
$apiUrl                 = 'http://'.$apiDomain.'/api/soap/?wsdl';
# API Username
$apiUsername            = 'demo';
# API Key
$apiPassword            = 'john.doe';
# Product SKU
$productSku             = 'hde004';
# Stock Item
$stockItem              = array(
    'qty'                   => 18, 
);
# Stock ID
$stockId                = 4;

$soapClient             = new SoapClient($apiUrl);
$sessionId              = $soapClient->login($apiUsername, $apiPassword);
$soapClient->call(
    $sessionId, 
    'product_stock.updateByStock', 
    array($productSku, $stockItem, $stockId)
);

/**
php shell/Innoexts/Warehouse/Cataloginventory/Stock/Item/Api/Update.php
 */