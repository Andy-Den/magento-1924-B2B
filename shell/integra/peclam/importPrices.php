<?php
require_once 'configIntegra.php';
if ($local == true ):$lines = file("$testDirectoryImport/products.csv", FILE_IGNORE_NEW_LINES);
else : $lines = file("$directoryImport/products.csv", FILE_IGNORE_NEW_LINES); endif;

// Limpa descontos

// Remove todos os preços de descontos da Distribuidora
$rmSpecialPrice = "DELETE FROM ";
$rmSpecialPrice .= $resource->getTableName(catalog_product_entity_decimal);
$rmSpecialPrice .= " WHERE attribute_id = 76 AND store_id = $storeId;";
echo $rmSpecialPrice . "\n\n\n";
$writeConnection->query($rmSpecialPrice);

// Remove todas as datas cadastradas para special prive
$rmSpecialDate = "DELETE FROM ";
$rmSpecialDate .= $resource->getTableName(catalog_product_entity_datetime);
$rmSpecialDate .= " WHERE attribute_id in (77,78) AND store_id = $storeId;";
echo $rmSpecialDate . "\n\n\n";
$writeConnection->query($rmSpecialDate);


// Verifica se o arquivo existe ou se existe dados no mesmo
if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
    $lines = array_unique($lines);
    foreach ($lines as $key => $value) {
        if ($key == 0) {
        } else {
            $i++;
            $temp = str_getcsv($value, '|');
            $skuErp = $temp[0];
            $priceErp = floatval($temp[5]);
            $specialPriceErp = floatval($temp[6]); //floatval($priceErp-(($priceErp*10)/100));

            // Pega o entity_id do produto para alterar o preço
            $getEntityId = "SELECT entity_id FROM ";
            $getEntityId .= $resource->getTableName(catalog_product_entity_varchar);
            $getEntityId .= " WHERE attribute_id = 185 AND store_id = $storeId AND `value` = $skuErp";

            $entityId = $readConnection->fetchOne($getEntityId);

            if($entityId == true){
                // Pega o valor do produto atual no site da distribuidora
                $getPrice = "SELECT `value` FROM ";
                $getPrice .= $resource->getTableName(catalog_product_entity_decimal);
                $getPrice .= " WHERE store_id = $storeId AND attribute_id = 75 AND entity_id = $entityId;";

                $price = $readConnection->fetchOne($getPrice);

                if($price == true){

                    if($price != $priceErp){
                        // atualiza o preço do produto caso seja diferente
                        $updatePrice = "UPDATE ";
                        $updatePrice .= $resource->getTableName(catalog_product_entity_decimal);
                        $updatePrice .= " SET `value` = $priceErp ";
                        $updatePrice .= "WHERE store_id = $storeId AND entity_id = $entityId AND `value` = $price;";

                        $writeConnection->query($updatePrice);
                    }
                } else {
                    // o produto ainda não possui preço, adiciona o preço
                    $setPrice = "INSERT INTO ";
                    $setPrice .= $resource->getTableName(catalog_product_entity_decimal);
                    $setPrice .= " (entity_type_id, attribute_id, store_id, entity_id, `value`) ";
                    $setPrice .= "VALUES (4, 75, $storeId, $entityId, $priceErp);";

                    $writeConnection->query($setPrice);
                }

                if($specialPriceErp == true){
                    // define desconto no valor do produto
                    $setSpecialPrice = "INSERT INTO ";
                    $setSpecialPrice .= $resource->getTableName(catalog_product_entity_decimal);
                    $setSpecialPrice .= " (entity_type_id, attribute_id, store_id, entity_id, `value`) ";
                    $setSpecialPrice .= "VALUES (4, 76, $storeId, $entityId, $specialPriceErp);";

                    $writeConnection->query($setSpecialPrice);

                    // define data para o desconto
                    $setFromSpecialPrice = "INSERT INTO ";
                    $setFromSpecialPrice .= $resource->getTableName(catalog_product_entity_datetime);
                    $setFromSpecialPrice .= " (entity_type_id, attribute_id, store_id, entity_id, `value`) ";
                    $setFromSpecialPrice .= "VALUES (4, 77, $storeId, $entityId, STR_TO_DATE($currentDate,'%Y-%m-%d %H:%i:%s'));";

                    $writeConnection->query($setFromSpecialPrice);

                    // define data para o desconto
                    $setToSpecialPrice = "INSERT INTO ";
                    $setToSpecialPrice .= $resource->getTableName(catalog_product_entity_datetime);
                    $setToSpecialPrice .= " (entity_type_id, attribute_id, store_id, entity_id, `value`) ";
                    $setToSpecialPrice .= "VALUES (4, 78, $storeId, $entityId, STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s'));";

                    $writeConnection->query($setToSpecialPrice);
                }
            }
            echo "linha --> $i\n";
        }
    }
}
