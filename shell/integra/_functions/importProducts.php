<?php

function limpaCorrigir($resource, $readConnection, $writeConnection, $idCategoryCorrigir) {
    /**
     * Verifica se existe produto cadastrado na categoria corrigir
     * caso exista remove todos.
     */
    $resultProductsCorrigir = "SELECT COUNT(product_id) FROM ";
    $resultProductsCorrigir .= $resource->getTableName(catalog_category_product);
    $resultProductsCorrigir .= " where category_id = $idCategoryCorrigir;";

    $resultProductsCorrigir = $readConnection->fetchOne($resultProductsCorrigir);

    if ($resultProductsCorrigir != 0) {

        $removeProductsCorrigir = "DELETE cpw FROM ";
        $removeProductsCorrigir .= $resource->getTableName(catalog_category_product) . " AS ccp";
        $removeProductsCorrigir .= " INNER JOIN " . $resource->getTableName(catalog_product_website) . " as cpw";
        $removeProductsCorrigir .= " ON ccp.product_id = cpw.product_id ";
        $removeProductsCorrigir .= " WHERE ccp.category_id = $idCategoryCorrigir;";

        $writeConnection->query($removeProductsCorrigir);

        $removeProductsCorrigir = "DELETE cpe FROM ";
        $removeProductsCorrigir .= $resource->getTableName(catalog_category_product) . " AS ccp";
        $removeProductsCorrigir .= " INNER JOIN " . $resource->getTableName(catalog_product_entity) . " as cpe";
        $removeProductsCorrigir .= " ON ccp.product_id = cpe.entity_id ";
        $removeProductsCorrigir .= " WHERE ccp.category_id = $idCategoryCorrigir;";

        $writeConnection->query($removeProductsCorrigir);
    }
}

function getIdErp($readConnection, $resource, $ignoreIdErp, $storeViewAll, $idErp) {
    if (in_array($idErp, $ignoreIdErp)) {
        echo "\n\n ID ERP ignorado na variável \$ignoreIdErp\n\n";
        return $entityId = NULL;
    } else {
        $getEntityId = "SELECT entity_id FROM ";
        $getEntityId .= $resource->getTableName(catalog_product_entity_varchar);
        $getEntityId .= " WHERE attribute_id = 185 AND `value` = '$idErp' AND store_id IN ($storeViewAll);";
        
        $entityId = $readConnection->fetchOne($getEntityId);
        return $entityId;
    }
}

function addNewProduct($resource, $writeConnection, $readConnection, $currentDateFormated, $entityId, $websiteId, $iStoreView, $name, $idErp) {
    // Caso a variavel $addNewProducts esteja true a integração irá adicioanr novos produtos dentro da categoria corrigir
    echo "adicionando produtos\n\n\n";
    // O produto não existe no cadastro cria o cadastro e põe na categoria corrigir
    $setProductEntity = "INSERT INTO ";
    $setProductEntity .= $resource->getTableName(catalog_product_entity);
    $setProductEntity .= " (entity_type_id, attribute_set_id, type_id, has_options, required_options, created_at)";
    $setProductEntity .= " VALUES(4, 15, 'simple', 0, 0, STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s'));";

    $writeConnection->query($setProductEntity);

    // Pega o Entity Id do produto adicionado
    $getEntityId = "SELECT entity_id FROM ";
    $getEntityId .= $resource->getTableName(catalog_product_entity);
    $getEntityId .= " WHERE created_at = $currentDateFormated;";

    $entityId = $readConnection->fetchOne($getEntityId);

    // Define o Website do produto
    $setWebsiteId = "INSERT INTO catalog_product_website
                                     (product_id, website_id)
                                     VALUES($entityId, $websiteId);";

    $writeConnection->query($setWebsiteId);

    // Define o nome para o novo produto para Whitelabel
    $setName = "INSERT INTO ";
    $setName .= $resource->getTableName(catalog_product_entity_varchar);
    $setName .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
    $setName .= " VALUES(4, 71, 0, $entityId, '$name');";

    $writeConnection->query($setName);

    $ln = 0;
    while ($ln < $iStoreView) {

        // Define o nome para o novo produto
        $setName = "INSERT INTO ";
        $setName .= $resource->getTableName(catalog_product_entity_varchar);
        $setName .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
        $setName .= " VALUES(4, 71, $arrayStoreView[$ln], $entityId, '$name');";

        $writeConnection->query($setName);

        // Define o ID_ERP para o produto
        $setIdErp = "INSERT INTO ";
        $setIdErp .= $resource->getTableName(catalog_product_entity_varchar);
        $setIdErp .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
        $setIdErp .= " VALUES(4, 185, $arrayStoreView[$ln], $entityId, '$idErp');";

        $writeConnection->query($setIdErp);

        $ln++;
    }
}