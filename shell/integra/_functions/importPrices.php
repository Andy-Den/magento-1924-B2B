<?php
// Function para denir valor default para os produtos que não possuem tabela de preços
function executeDefaultPrices($lines, $resource, $readConnection, $writeConnection, $storeId, $iStoreView, $arrayStoreView, $websiteId, $skuErp, $price) {
    echo "\nTrabalhando preço padrão para storeId = $storeId\n";

    if ($price > 0){
        $getProductEntity = "SELECT entity_id FROM ";
        $getProductEntity .= $resource->getTableName(catalog_product_entity_varchar);
        $getProductEntity .= " WHERE `value` = '$skuErp' AND store_id = $storeId AND attribute_id = 185";
        $getProductEntity .= " ORDER BY store_id ASC LIMIT 1;";

        $entityId = $readConnection->fetchOne($getProductEntity);

        // Pega o valor atual do produto
        $getFinalPriceStoreDefault = "SELECT `value` FROM ";
        $getFinalPriceStoreDefault .= $resource->getTableName(catalog_product_entity_decimal);
        $getFinalPriceStoreDefault .= " WHERE attribute_id = 75 AND store_id = 0 ";
        $getFinalPriceStoreDefault .= " AND entity_id = $entityId;";

        $priceIntegraStoreDefault = $readConnection->fetchOne($getFinalPriceStoreDefault);

        // caso o não exista o valor do produto na store default
        if ((!$priceIntegraStoreDefault) && (!is_null($priceIntegraStoreDefault))) {
            $setFinalPrice = "INSERT INTO ";
            $setFinalPrice .= $resource->getTableName(catalog_product_entity_decimal);
            $setFinalPrice .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
            $setFinalPrice .= " VALUES(4, 75, 0, $entityId, NULL)";

            $writeConnection->query($setFinalPrice);
        }

        $getFinalPrice = NULL;
        $priceIntegra = NULL;
        $getFinalPrice = "SELECT `value` FROM ";
        $getFinalPrice .= $resource->getTableName(catalog_product_entity_decimal);
        $getFinalPrice .= " WHERE attribute_id = 75 AND store_id = $storeId ";
        $getFinalPrice .= " AND entity_id = $entityId;";

        $priceIntegra = $readConnection->fetchOne($getFinalPrice);
        $priceIntegra = floatval($priceIntegra);

        // Pega o maior valor dos grupos
        $getGroupPrice = "SELECT `value` FROM ";
        $getGroupPrice .= $resource->getTableName(catalog_product_entity_group_price);
        $getGroupPrice .= " WHERE entity_id = $entityId AND website_id = $websiteId ORDER BY `value` DESC LIMIT 1;";

        $groupPrice = $readConnection->fetchOne($getGroupPrice);

        if (!$priceIntegra) {
            $setFinalPrice = "INSERT INTO ";
            $setFinalPrice .= $resource->getTableName(catalog_product_entity_decimal);
            $setFinalPrice .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
            $setFinalPrice .= " VALUES(4, 75, $storeId, $entityId, $price)";

            $writeConnection->query($setFinalPrice);
        }
        if (($groupPrice > $priceIntegra)) {
            $updatePrice = "UPDATE ";
            $updatePrice .= $resource->getTableName(catalog_product_entity_decimal);
            $updatePrice .= " SET `value` = $price";
            $updatePrice .= " WHERE attribute_id = 75 AND entity_id = $entityId AND store_id = $storeId;";

            $writeConnection->query($updatePrice);
        }
        if ($groupPrice > $price) {
            $updatePrice = "UPDATE ";
            $updatePrice .= $resource->getTableName(catalog_product_entity_decimal);
            $updatePrice .= " SET `value` = $groupPrice";
            $updatePrice .= " WHERE attribute_id = 75 AND entity_id = $entityId AND store_id = $storeId;";

            $writeConnection->query($updatePrice);
        }
    }
}

// Function para definir valores de produtos com tabela de preços
function executeTablePrices($lines, $resource, $readConnection, $writeConnection, $codeStore, $storeId, $iStoreView, $arrayStoreView, $websiteId, $idErpTabela, $nameTablePrice, $skuErp, $price) {

    if ($price > 0) {
        if (!$idErpTabela): $idErpTabela = $codeStore; endif;

        $getGroupId = "SELECT customer_group_id FROM ";
        $getGroupId .= $resource->getTableName(customer_group);
        $getGroupId .= " WHERE website_id = $websiteId AND id_tabela = '$idErpTabela';";

        $groupId = $readConnection->fetchOne($getGroupId);

        $getProductEntity = "SELECT entity_id FROM ";
        $getProductEntity .= $resource->getTableName(catalog_product_entity_varchar);
        $getProductEntity .= " WHERE `value` = '$skuErp' AND store_id = $storeId AND attribute_id = 185";
        $getProductEntity .= " ORDER BY store_id ASC LIMIT 1;";

        $entityId = $readConnection->fetchOne($getProductEntity);

        // Condição para os grupos válidos definidos no arquivo configIntegra
        if ($groupId == false) {

            $setGroup = "INSERT IGNORE INTO ";
            $setGroup .= $resource->getTableName(customer_group);
            $setGroup .= " (customer_group_code, tax_class_id, website_id, id_tabela)";
            $setGroup .= " VALUES('$nameTablePrice', 3, $websiteId, '$idErpTabela')";

            $writeConnection->query($setGroup);

        } else {
            $updateIdGroup = "UPDATE ";
            $updateIdGroup .= $resource->getTableName(customer_group);
            $updateIdGroup .= " SET id_tabela = '$idErpTabela'";
            $updateIdGroup .= " WHERE customer_group_id = '$groupId';";

            $writeConnection->query($updateIdGroup);
        }

        if ($groupId == true) {

            if (($entityId == true) && ($price > 0.00)) {

                $ln = 1;
                while ($ln <= $iStoreView) {
                    $getPrice = "SELECT value_id FROM ";
                    $getPrice .= $resource->getTableName(catalog_product_entity_group_price);
                    $getPrice .= " WHERE entity_id = $entityId AND website_id = $websiteId AND customer_group_id = '$groupId';";

                    $priceProduct = $readConnection->fetchOne($getPrice);

                    if ($priceProduct == true) {
                        $updatePrice = "UPDATE ";
                        $updatePrice .= $resource->getTableName(catalog_product_entity_group_price);
                        $updatePrice .= " SET `value` = $price";
                        $updatePrice .= " WHERE entity_id = $entityId AND website_id = $websiteId AND customer_group_id = '$groupId';";

                        $writeConnection->query($updatePrice);

                    } else {
                        $setPrice = "INSERT IGNORE INTO ";
                        $setPrice .= $resource->getTableName(catalog_product_entity_group_price);
                        $setPrice .= " (entity_id, all_groups, customer_group_id, `value`, website_id) ";
                        $setPrice .= " VALUES($entityId, 0, '$groupId', $price, $websiteId); ";

                        $writeConnection->query($setPrice);
                    }

                    $tmpStoreView = $arrayStoreView[$ln - 1];
                    echo "\nTrabalhando Tabela de Preço para storeId = $tmpStoreView\n";

                    // Pega o valor atual do produto
                    $getFinalPrice = "SELECT `value` FROM ";
                    $getFinalPrice .= $resource->getTableName(catalog_product_entity_decimal);
                    $getFinalPrice .= " WHERE attribute_id = 75 AND store_id = $tmpStoreView ";
                    $getFinalPrice .= " AND entity_id = $entityId;";

                    $priceIntegra = $readConnection->fetchOne($getFinalPrice);
                    $priceIntegra = floatval($priceIntegra);

                    if ((($priceIntegra == 0))) {
                        $setFinalPrice = "INSERT INTO ";
                        $setFinalPrice .= $resource->getTableName(catalog_product_entity_decimal);
                        $setFinalPrice .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setFinalPrice .= " VALUES(4, 75, $tmpStoreView, $entityId, $price)";

                        $writeConnection->query($setFinalPrice);
                    }

                    // Pega o maior valor dos grupos
                    $getGroupPrice = "SELECT `value` FROM ";
                    $getGroupPrice .= $resource->getTableName(catalog_product_entity_group_price);
                    $getGroupPrice .= " WHERE entity_id = $entityId AND website_id = $websiteId ORDER BY `value` DESC LIMIT 1;";

                    $groupPrice = $readConnection->fetchOne($getGroupPrice);

                    $i = 0;

                    while ($i <= $iStoreView -1) {


                        if (!$priceIntegra) {
                            $setFinalPrice = "INSERT IGNORE INTO ";
                            $setFinalPrice .= $resource->getTableName(catalog_product_entity_decimal);
                            $setFinalPrice .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setFinalPrice .= " VALUES(4, 75, $arrayStoreView[$i], $entityId, $price)";

                            $writeConnection->query($setFinalPrice);

                        }
                        if (($groupPrice > $priceIntegra)) {
                            $updatePrice = "UPDATE ";
                            $updatePrice .= $resource->getTableName(catalog_product_entity_decimal);
                            $updatePrice .= " SET `value` = $price";
                            $updatePrice .= " WHERE attribute_id = 75 AND entity_id = $entityId AND store_id = $arrayStoreView[$i];";

                            $writeConnection->query($updatePrice);

                        }
                        if ($groupPrice > $price) {
                            $updatePrice = "UPDATE ";
                            $updatePrice .= $resource->getTableName(catalog_product_entity_decimal);
                            $updatePrice .= " SET `value` = $groupPrice";
                            $updatePrice .= " WHERE attribute_id = 75 AND entity_id = $entityId AND store_id = $arrayStoreView[$i];";

                            $writeConnection->query($updatePrice);

                        }

                        $i++;
                    }


                    $ln++;
                }

            } else {
                echo "O produto não foi localizado\n\n\n";
            }
        } else {
            echo "Grupo de cliente não existe\n\n\n";
        }
    }
}

function applySpecialPrice($resource, $readConnection, $writeConnection, $storeId, $skuErp, $specialPrice){
    /**
     * Atributos:
     * 76 -> special_price
     * 77 -> special_from_date
     * 78 -> special_to_date
     */

    echo "\nTrabalhando preço especial para storeId = $storeId\n";

    $getProductEntity = "SELECT entity_id FROM ";
    $getProductEntity .= $resource->getTableName(catalog_product_entity_varchar);
    $getProductEntity .= " WHERE `value` = '$skuErp' AND store_id = $storeId AND attribute_id = 185";
    $getProductEntity .= " ORDER BY store_id ASC LIMIT 1;";

    $entityId = $readConnection->fetchOne($getProductEntity);

    $getSpecialPrice = "SELECT `value` FROM catalog_product_entity_decimal WHERE attribute_id = 76 AND entity_id = $entityId AND store_id = $storeId;";

    $specialPriceIntegra = $readConnection->fetchOne($getSpecialPrice);

    if (!$specialPriceIntegra) {
        $addSpecialPrice = "INSERT INTO catalog_product_entity_decimal (entity_type_id, attribute_id, store_id, entity_id, `value`) VALUES (4, 76, $storeId, $entityId, $specialPrice);";

        $writeConnection->query($addSpecialPrice);
    }

    if ($specialPriceIntegra != $specialPrice) {
        $updateSpecialPrice = "UPDATE catalog_product_entity_decimal SET `value` = $specialPrice WHERE attribute_id = 76 AND entity_id = $entityId AND store_id = $storeId;";

        $writeConnection->query($updateSpecialPrice);
    }

}
