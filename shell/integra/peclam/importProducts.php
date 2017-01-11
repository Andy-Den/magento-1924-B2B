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
$resultProductsCorrigir = "SELECT COUNT(product_id) FROM ";
$resultProductsCorrigir .= $resource->getTableName(catalog_category_product);
$resultProductsCorrigir .= " where category_id = $idCategoryCorrigir;";

$resultProductsCorrigir = $readConnection->fetchOne($resultProductsCorrigir);

if ($resultProductsCorrigir != 0){
    $removeProductsCorrigir = "DELETE cpe FROM ";
    $removeProductsCorrigir .= $resource->getTableName(catalog_category_product) . " AS ccp";
    $removeProductsCorrigir .= " INNER JOIN " . $resource->getTableName(catalog_product_entity ) . " as cpe";
    $removeProductsCorrigir .= " ON ccp.product_id = cpe.entity_id ";
    $removeProductsCorrigir .= " WHERE ccp.category_id = $idCategoryCorrigir;";

    $writeConnection->query($removeProductsCorrigir);
}

// Monta o cabeçalho do arquivo
$statusAlteradoHead = "ID Integra|ID ERP|Nome|Antes|Agora\n";
$visibilidadeAlteradaHead = "ID Integra|ID ERP|Nome|Antes|Agora\n";

if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
    foreach ($lines as $key => $value) {
        if  ( $key == 0 ) { }
        else {

            $value = str_replace("'", "", $value);

            $temp = str_getcsv($value, '|');
            $idErp = $temp[0];
            $name = $temp[4];
            $status = $temp[12];
            $qty = 0;
            $disp = 4;

            // Procura o produto pelo id ERP
            $getEntityId = "SELECT entity_id FROM ";
            $getEntityId .= $resource->getTableName(catalog_product_entity_varchar);
            $getEntityId .= " WHERE attribute_id = 185 AND `value` = '$idErp' AND store_id = $storeviewId";

            $entityId = $readConnection->fetchOne($getEntityId);

            if (empty($entityId)) {
                // Procura o produto pelo nome ERP ou nome no Whitelabel
                $getEntityId = "SELECT entity_id FROM ";
                $getEntityId .= $resource->getTableName(catalog_product_entity_varchar);
                $getEntityId .= " WHERE attribute_id = 71 AND `value` = '$name' AND store_id IN (0,$storeviewId) LIMIT 1";

                $entityId = $readConnection->fetchOne($getEntityId);

                // Se o $entityId ainda retornar vazio adiciona o produto com seus atributos e adiciona na categoria corrigir:
                if (empty($entityId)) {
                    // O produto não existe no cadastro cria o cadastro e põe na categoria corrigir
                    $setProductEntity = "INSERT INTO ";
                    $setProductEntity .= $resource->getTableName(catalog_product_entity);
                    $setProductEntity .= " (entity_type_id, attribute_set_id, type_id, has_options, required_options, created_at)";
                    $setProductEntity .= " VALUES(4, 15, 'simple', 0, 0, STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s'));";


                    $writeConnection->query($setProductEntity);

                    // Pega o Entity Id do produto adicionado
                    $getEntityId = "SELECT entity_id FROM ";
                    $getEntityId .= $resource->getTableName(catalog_product_entity);
                    $getEntityId .= " ORDER BY entity_id DESC LIMIT 1;";

                    $entityId = $readConnection->fetchOne($getEntityId);

                    if (!empty($entityId)){
                        // Seta a categoria corrigir para o produto
                        $setCategoryCorrigir = "INSERT INTO ";
                        $setCategoryCorrigir .= $resource->getTableName(catalog_category_product);
                        $setCategoryCorrigir .= " (category_id, product_id, position)";
                        $setCategoryCorrigir .= " VALUES($idCategoryCorrigir, $entityId, 0);";

                        $writeConnection->query($setCategoryCorrigir);
                    }
                }
            }

            echo "\n\n >>>>>>>>>> Entity ID do produto: $entityId \n\n\n";

            // Verifica se já existe um idErp do produto definido para o produto e cadastra ou atualiza
            $iStoreView = count($storeView);
            $ln = $iStoreView;
            while ($ln <= $iStoreView) {
                $tmpStoreView = $storeView[$ln - 1];

                // Define o idErp para o produto
                $getIdErp = "SELECT `value` FROM ";
                $getIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                $getIdErp .= " WHERE attribute_id = 185 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                $resultIdErp = $readConnection->fetchOne($getIdErp);

                if (!empty($resultIdErp)) {
                    if ($resultIdErp != $idErp) {
                        // O id ERP informado pelo CSV é diferente da descrição cadastrada no sitema
                        $updateIdErp = "UPDATE ";
                        $updateIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                        $updateIdErp .= " SET `value` = '$idErp'";
                        $updateIdErp .= " WHERE attribute_id = 185 AND store_id = $tmpStoreView AND `value` = '$resultIdErp';";

                        $writeConnection->query($updateIdErp);
                    }
                } else {
                    // Não existe um idErp para o produto definido então vamos adicionar um
                    $setIdErp = "INSERT INTO ";
                    $setIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                    $setIdErp .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                    $setIdErp .= " VALUES(4, 185, $tmpStoreView, $entityId, '$idErp');";

                    $writeConnection->query($setIdErp);
                }

                // Verifica se já existe uma visibilidade definida para o produto e cadastra ou atualiza
                $getVisibilty = "SELECT `value` FROM ";
                $getVisibilty .= $resource->getTableName(catalog_product_entity_int);
                $getVisibilty .= " WHERE attribute_id = 102 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                $resultVisibility = $readConnection->fetchOne($getVisibilty);

                if (!empty($resultVisibility)) {
                    if ($resultVisibility != $disp) {
                        // A visibilidade informada pelo CSV é diferente da visibilidade cadastrada no sitema
                        $updateProductVisibility = "UPDATE ";
                        $updateProductVisibility .= $resource->getTableName(catalog_product_entity_int);
                        $updateProductVisibility .= " SET `value` = $disp";
                        $updateProductVisibility .= " WHERE attribute_id = 102 AND store_id = $tmpStoreView AND `value` = $resultVisibility AND entity_id = $entityId;";

                        $writeConnection->query($updateProductVisibility);

                        if ($resultVisibility == 4): $resultVisibility = 'Catálogo Busca'; else: $resultVisibility = 'Não exibir individualmente'; endif;
                        if ($disp == 4): $dispLog = 'Catálogo Busca'; else: $dispLog = 'Não exibir individualmente'; endif;

                        $visibilidadeAlterada .= "$entityId|$idErp|$name|$status\n";

                    }
                } else {
                    // Não existe visibilidade definida então vamos adicionar uma
                    $setVisibility = "INSERT INTO ";
                    $setVisibility .= $resource->getTableName(catalog_product_entity_int);
                    $setVisibility .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                    $setVisibility .= " VALUES(4, 102, $tmpStoreView, $entityId, $disp);";

                    $writeConnection->query($setVisibility);

                    if ($disp == 4): $dispLog = 'Catálogo Busca'; else: $dispLog = 'Não exibir individualmente'; endif;
                    $visibilidadeAlterada .= "$entityId|$idErp|$name|S|$dispLog\n";
                }

                // Verifica se já existe um status definido para o produto e cadastra ou atualiza
                $getStatus = "SELECT `value` FROM ";
                $getStatus .= $resource->getTableName(catalog_product_entity_int);
                $getStatus .= " WHERE attribute_id = 96 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                $resultStatus = $readConnection->fetchOne($getStatus);

                if (!empty($resultStatus)) {
                    if ($resultStatus != $status) {
                        // O status informado pelo CSV é diferente do status cadastrado no sitema
                        $updateProductStatus = "UPDATE ";
                        $updateProductStatus .= $resource->getTableName(catalog_product_entity_int);
                        $updateProductStatus .= " SET `value` = $status";
                        $updateProductStatus .= " WHERE attribute_id = 96 AND store_id = $tmpStoreView AND `value` = $resultStatus AND entity_id = $entityId;";

                        $writeConnection->query($updateProductStatus);

                        if ($resultStatus == 1): $resultStatus = 'Habilitado'; else: $resultStatus = 'Desabilitado'; endif;
                        if ($status == 1): $status = 'Habilitado'; else: $status = 'Desabilitado'; endif;

                        $statusAlterado .= "$entityId|$idErp|$name|$resultStatus|$status\n";
                    }
                } else {
                    // Não existe status definido então vamos adicionar um
                    $setStatus = "INSERT INTO ";
                    $setStatus .= $resource->getTableName(catalog_product_entity_int);
                    $setStatus .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                    $setStatus .= " VALUES(4, 96, $tmpStoreView, $entityId, $status);";

                    $writeConnection->query($setStatus);

                    if ($status == 1): $status = 'Habilitado'; else: $status = 'Desabilitado'; endif;
                    $statusAlterado .= "$entityId|$idErp|$name|S|$status\n";
                }

                $ln++;
            }

            // Define o website que o produto deve estar
            if (!empty($entityId)) {
                $getProductWebSite = "SELECT website_id FROM ";
                $getProductWebSite .= $resource->getTableName(catalog_product_website);
                $getProductWebSite .= " WHERE product_id = $entityId AND website_id = $websiteId;";

                $productWebSiteId = $readConnection->fetchOne($getProductWebSite);

                if (empty($productWebSiteId)) {
                    $setProductWebSite = "INSERT INTO ";
                    $setProductWebSite .= $resource->getTableName(catalog_product_website);
                    $setProductWebSite .= " (product_id, website_id)";
                    $setProductWebSite .= " VALUES($entityId, $websiteId);";

                    $writeConnection->query($setProductWebSite);
                }
            }

            // Verifica se já existe um nome definido para o produto na store e cadastra um ou atualiza
            $getName = "SELECT `value` FROM ";
            $getName .= $resource->getTableName(catalog_product_entity_varchar);
            $getName .= " WHERE attribute_id = 71 AND store_id = $storeId AND entity_id = $entityId;";

            $resultNameStore = $readConnection->fetchOne($getName);

            // Verifica se o nome do produto já está cadastrado no Whitelabel
            $getName = "SELECT `value` FROM ";
            $getName .= $resource->getTableName(catalog_product_entity_varchar);
            $getName .= " WHERE attribute_id = 71 AND store_id = 0 AND entity_id = $entityId;";

            $resultNameWhitelabel = $readConnection->fetchOne($getName);

                if ($resultNameStore != $name) {
                    // O nome do produto informado pelo CSV é diferente da descrição cadastrada no sitema
                    $updateProductName = "UPDATE ";
                    $updateProductName .= $resource->getTableName(catalog_product_entity_varchar);
                    $updateProductName .= " SET `value` = '$name'";
                    $updateProductName .= " WHERE attribute_id = 71 AND store_id = $storeId AND entity_id = $entityId;";

                    $writeConnection->query($updateProductName);
                }
                if (!$resultNameStore) {
                    // Não existe um nome definido então vamos adicionar um no whitelabel
                    $setName = "INSERT INTO ";
                    $setName .= $resource->getTableName(catalog_product_entity_varchar);
                    $setName .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                    $setName .= " VALUES(4, 71, $storeId, $entityId, '$name');";

                    $writeConnection->query($setName);
                }
                if (empty($resultNameWhitelabel)) {
                    // Não existe um nome definido então vamos adicionar um no whitelabel
                    $setName = "INSERT INTO ";
                    $setName .= $resource->getTableName(catalog_product_entity_varchar);
                    $setName .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                    $setName .= " VALUES(4, 71, 0, $entityId, '$name');";

                    $writeConnection->query($setName);
                }

            if ($mngrStock == true) {
                // Atualiza Stock
                $getStock = "SELECT qty FROM ";
                $getStock .= $resource->getTableName(cataloginventory_stock_item);
                $getStock .= " WHERE stock_id = $stockId AND product_id = $entityId;";

                $resultQtyStock = $readConnection->fetchOne($getStock);

                if (!empty($resultQtyStock)) {
                    if ($resultQtyStock != $qty) {
                        // A quantidade informada pelo CSV é diferente da quantidade cadastrada no sitema
                        $updateQty = "UPDATE ";
                        $updateQty .= $resource->getTableName(cataloginventory_stock_item);
                        $updateQty .= " SET qty = $qty, is_in_stock = $disp";
                        $updateQty .= " WHERE stock_id = $stockId AND product_id = $entityId;";

                        $writeConnection->query($updateQty);
                    }
                } else {
                    // Não existe um Stock para o produto definido então vamos adicionar um
                    $setStock = "INSERT INTO ";
                    $setStock .= $resource->getTableName(cataloginventory_stock_item);
                    $setStock .= " (product_id, stock_id, qty, min_qty, use_config_min_qty, is_qty_decimal, backorders, use_config_backorders, min_sale_qty, use_config_min_sale_qty, max_sale_qty, use_config_max_sale_qty, is_in_stock, low_stock_date, notify_stock_qty, use_config_notify_stock_qty, manage_stock, use_config_manage_stock, stock_status_changed_auto, use_config_qty_increments, qty_increments, use_config_enable_qty_inc, enable_qty_increments, is_decimal_divided)";
                    $setStock .= " VALUES($entityId, $stockId, 0, $qty, 0, 1, 0, 0, 0, 1, 0, 0, $disp, NULL, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0);";

                    $writeConnection->query($setStock);
                }
            }
        }
    }

    mkdir("$directoryRep/products/$currentDate", 0777, true);

    // Gera o arquivo de produtos com status alterados
    if (!is_null($statusAlterado)){
        $outFileStatus = $statusAlteradoHead . $statusAlterado;
        file_put_contents("$directoryRep/products/$currentDate/produtosStatus.csv", $outFileStatus);
        echo "\n Arquivo de produtos com status alterado foi criado.\n";
    }

    // Gera o arquivo de produtos com status alterados
    if (!is_null($visibilidadeAlterada)){
        $outFileVisibilidade = $visibilidadeAlteradaHead . $visibilidadeAlterada;
        file_put_contents("$directoryRep/products/$currentDate/produtosVisibilidade.csv", $outFileVisibilidade);
        echo "\n Arquivo de produtos com visibilidade alterada foi criado.\n";
    }
}