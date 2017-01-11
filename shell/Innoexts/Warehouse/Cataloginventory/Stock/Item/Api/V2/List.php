<?php

# API DOMAIN
$apiDomain              = 'magento7.localhost';
# API URL
$apiUrl                 = 'http://'.$apiDomain.'/api/v2_soap/?wsdl';
# API Username
$apiUsername            = 'demo';
# API Key
$apiPassword            = 'john.doe';
# Product SKUs
$productSkus            = array('hde004', 'hde005');
# Stock ID
$stockId                = 4;

$soapClient             = new SoapClient($apiUrl, array('trace' => 1));
$sessionId              = $soapClient->login($apiUsername, $apiPassword);
$responce               = $soapClient->catalogInventoryStockItemListByStock($sessionId, $productSkus, $stockId);

print_r($responce);

/**
php shell/Innoexts/Warehouse/Cataloginventory/Stock/Item/Api/V2/List.php
 */