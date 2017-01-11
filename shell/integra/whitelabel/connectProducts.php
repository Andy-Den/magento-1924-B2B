<?php

require_once './configIntegra.php';
/**
 * Inicia a integração de produtos atualizando o status (quantidade e disponibilidade),
 */
$lines = file("$directoryImp/products.csv", FILE_IGNORE_NEW_LINES);

/**
 * Verifica se existe produto cadastrado na categoria corrigir
 * caso exista remove todos.
 */
$resultProductsCorrigir = " SELECT COUNT(product_id) FROM ";
$resultProductsCorrigir .= $resource->getTableName(catalog_category_product);
$resultProductsCorrigir .= " where category_id = $idCategoryCorrigir;";

$resultProductsCorrigir = $readConnection->fetchOne($resultProductsCorrigir);

if ($resultProductsCorrigir != 0) {
    $removeProductsCorrigir = "DELETE FROM ";
    $removeProductsCorrigir .= $resource->getTableName(catalog_category_product);
    $removeProductsCorrigir .= " WHERE category_id=$idCategoryCorrigir;";

    $writeConnection->query($removeProductsCorrigir);
}
$i = 0;
if (empty($lines)) {
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
    foreach ($lines as $key => $value) {
        $i ++;
        $temp = str_getcsv($value, '|', "'");
        $storeCode = $temp[0];
        $webSite = $temp[1];
        $visibility = $temp[2];
        $categories = $temp[3];
        $sku = $temp[4];
        $idErp = $temp[5];
        $name = $temp[6];
        $status = $temp[7];
        $storeId = $temp[8];

        echo $idErp . "\n\n";

        /** Levando em conta que todo produto que está na lista é um produto novo faremos a verificação básica dicionando
         * o produto na categoria corrigir apenas caso o SKU seja igual ao de um produto já cadastrado
         * Verifica se o SKU global informado já existe * */
        $getSKUIntegra = "SELECT entity_id FROM ";
        $getSKUIntegra .= $resource->getTableName(catalog_product_entity);
        $getSKUIntegra .= " WHERE sku = '$sku';";
        
        $entityId = $readConnection->fetchOne($getSKUIntegra);

        if (!$entityId) {
            $skuNaoLocalizado .= "$sku\n";
        } else {
            // Verifica se o ID_ERP já está cadastrado
            $getIdErp = "SELECT * FROM catalog_product_entity_varchar WHERE attribute_id = 185 AND `value` = $idErp;";

            $idErpIntegra = $readConnection->fetchOne($getIdErp);

//        var_dump($idErpIntegra); exit;

            if (!$idErpIntegra):
                $setIdErp = "INSERT INTO catalog_product_entity_varchar
                        (entity_type_id, attribute_id, store_id, entity_id, `value`)
                        VALUES(4, 185, $storeId, $entityId, '$idErp');";

                $writeConnection->query($setIdErp);
            endif;

            // Verifica se o produto já está associado ao website
            $getProdWebsite = "SELECT * FROM catalog_product_website
                            WHERE product_id = $entityId AND website_id = $websiteId";

            $prodWebsite = $readConnection->fetchOne($getProdWebsite);

            if (!$prodWebsite):
                $setProdWebsite = "INSERT INTO whitelabel.catalog_product_website
                                (product_id, website_id)
                                VALUES($entityId, $websiteId);";

                $writeConnection->query($setProdWebsite);
            endif;

            // Adiciona os produtos na categoria na store da distribuidora
            $arrayCategories = explode(',', $categories);
            $iCategories = count($arrayCategories);
            $ln = 1;
            while ($ln <= $iCategories) {
                $tmpCategory = $arrayCategories[$ln - 1];

                $setCategory = "INSERT IGNORE INTO ";
                $setCategory .= $resource->getTableName(catalog_category_product);
                $setCategory .= " (category_id, product_id, position)";
                $setCategory .= " VALUES($tmpCategory, $entityId, 0);";

                $writeConnection->query($setCategory);

                $ln ++;
            }
        }
    }
}
