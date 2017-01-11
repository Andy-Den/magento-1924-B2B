<?php

# API DOMAIN
$apiDomain              = 'magento7.localhost';
# API URL
$apiUrl                 = 'http://'.$apiDomain.'/api/v2_soap/?wsdl';
# API Username
$apiUsername            = 'demo';
# API Key
$apiPassword            = 'john.doe';
# Product SKU
$productSku             = 'hde004';
# Stock Item
$stockItem              = array(
    'qty'                   => 15, 
);
# Stock ID
$stockId                = 4;

$soapClient             = new SoapClient($apiUrl, array('trace' => 1));
$sessionId              = $soapClient->login($apiUsername, $apiPassword);
$soapClient->catalogInventoryStockItemUpdateByStock($sessionId, $productSku, $stockItem, $stockId);

/**
php shell/Innoexts/Warehouse/Cataloginventory/Stock/Item/Api/V2/Update.php
 */