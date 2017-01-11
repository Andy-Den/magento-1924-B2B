<?php

require_once 'configIntegra.php';
require_once '../_functions/importPrices.php';

$lines = file("$directoryImp/table_prices/table_prices.csv", FILE_IGNORE_NEW_LINES);

// Verifica se o table_prices ou se existe se não exite procura pelo table_prices_virtual
if (empty($lines)) {
    echo "\n\n Arquivo vazio ou não existe\n\n";
} else {
    $lines = array_unique($lines);
    foreach ($lines as $key => $value) {
        $i++;
        $temp = str_getcsv($value, '|', "'");
        $idErpTabela = $temp[0];
        $nameTablePrice = "$temp[1]";
        $skuErp = $temp[2];
        $price = floatval(str_replace(',', '.', $temp[3]));
        $specialPrice = floatval(str_replace(',', '.', $temp[4]));

        // Valida se o valor do produto é de tabela de preço ou valor padrão.
        if (!$idErpTabela || !$nameTablePrice) {
            executeDefaultPrices($lines, $resource, $readConnection, $writeConnection, $storeId, $iStoreView, $arrayStoreView, $websiteId, $skuErp, $price);
        }
        if (($idErpTabela || $nameTablePrice) && (in_array($idErpTabela, $idTablePriceValid))) {
            executeTablePrices($lines, $resource, $readConnection, $writeConnection, $codeStore, $storeId, $iStoreView, $arrayStoreView, $websiteId, $idErpTabela, $nameTablePrice, $skuErp, $price);
        }
        if ($specialPrice) {
            //applySpecialPrice($resource, $readConnection, $writeConnection, $storeId, $skuErp, $specialPrice);
        }
        echo "linha --> $i\n";
    }
}
