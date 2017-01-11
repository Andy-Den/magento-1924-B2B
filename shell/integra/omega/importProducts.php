<?php
require_once './configIntegra.php';
/**
 * Inicia a integração de produtos atualizando o status (quantidade e disponibilidade),
 */

$lines = file("$directoryImp/products/products.csv", FILE_IGNORE_NEW_LINES);

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

$i = 1;
if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
    foreach ($lines as $key => $value) {
        $temp = str_getcsv($value, '|', "'");
        $idErp = $temp[0];
        $name = str_replace('\'', '`', $temp[1]);
        $status = $temp[2];
        $qty = intval($temp[3]);
        $disp = $temp[4];

        // Define a quantidade disponível no estoque do cliente
        if ($qty <= 0): $disp = 0; else: $disp = 1; endif;

        // Procura o produto pelo id ERP
        $getEntityId = "SELECT entity_id FROM ";
        $getEntityId .= $resource->getTableName(catalog_product_entity_varchar);
        $getEntityId .= " WHERE attribute_id = 185 AND `value` = '$idErp' AND store_id = $storeviewId";

        $entityId = $readConnection->fetchOne($getEntityId);

        if (in_array($idErp, $ignoreIdErp)) {
            echo "\n\n ID ERP ignorado na variável \$ignoreIdErp\n\n";
        } else {
            if (empty($entityId)) {
                echo "\n\n >>>>>>>>>>>>>>>>>>$ln \n\n";
                // Caso a variavel $addNewProducts esteja true a integração irá adicioanr novos produtos dentro da categoria corrigir
                if ($addNewProducts == true) {
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
                    $getEntityId .= " ORDER BY entity_id DESC LIMIT 1;";

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

                    // Adiciona o produto na categoria Corrigir
                    $setCategoryCorrigir = "INSERT INTO ";
                    $setCategoryCorrigir .= $resource->getTableName(catalog_category_product);
                    $setCategoryCorrigir .= " (category_id, product_id, position)";
                    $setCategoryCorrigir .= " VALUES($idCategoryCorrigir, $entityId, 0);";

                    $writeConnection->query($setCategoryCorrigir);

                    echo "\n\n >>>>>>>>>> Entity ID do produto: $entityId \n\n\n";
                    $newProducts .= "$idErp|$name|$qty\n";
                } else {
                    echo "\n\n Modo de adição de novos produtos desabilitado!\n\n";
                }
            } else {
                // Verifica se já existe um idErp do produto definido para o produto e cadastra ou atualiza
                $arrayStoreView = explode(',', $storeView);
                $iStoreView = count($arrayStoreView);

                echo " $iStoreView ---- $idErp";

                $ln = 1;
                while ($ln <= $iStoreView) {
                    $tmpStoreView = $arrayStoreView[$ln -1];

                    echo "\n\n Trabalhando para o Store ID: $tmpStoreView.\n\n";
                    // Define o idErp para o produto
                    $getIdErp = "SELECT `value` FROM ";
                    $getIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                    $getIdErp .= " WHERE attribute_id = 185 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                    $resultIdErp = $readConnection->fetchOne($getIdErp);

                    if (!empty($resultIdErp)) {
                        if ($resultIdErp!= $idErp) {
                            // O id ERP informado pelo CSV é diferente do ID_ERP cadastrado no sitema
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

                        // seta o atributo para storeId 0
                        $setIdErp = "INSERT IGNORE INTO ";
                        $setIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                        $setIdErp .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setIdErp .= " VALUES(4, 185, 0, $entityId, '$idErp');";

                        $writeConnection->query($setIdErp);
                    }



                    // Pega a categoria do produto que irá definir a visibilidade na store view:
                    // Configura a visibilidade dos produtos nas stores views

                    // Define a visibilidade para todos os produtos na storeView Omega Putina
                    if ($tmpStoreView == 4){
                        if (($status == 1) && ($qty > 0)): $visibility = 4; else: $visibility = 1; endif;
                    }

                    if (($tmpStoreView == 5) || ($tmpStoreView == 7) ) {
                        // A store view é 5 Omega MSD ou 7 MSD Omega e o produto está cadastrado na categoria MSD (265)
                        // então visibilidade = catalogo, busca (4)
                        $getCategory = "SELECT category_id FROM ";
                        $getCategory .= $resource->getTableName(catalog_category_product);
                        $getCategory .= " WHERE category_id = 265 AND product_id = $entityId";

                        $categoryId = $readConnection->fetchOne($getCategory);

                        // Caso o produto seja da categoria MSD e a quantidade no stock seja 1 ou mais ele exibe o produto
                        if (($categoryId == true) && ($qty > 0)): $visibility = 4; else: $visibility = 1; endif;

                    }
                    if ($tmpStoreView == 6) {
                        // A store view é Purina (6) e o produto está cadastrado na categoria Purina (241)
                        // então visibilidade = catalogo, busca (4)
                        $getCategory = "SELECT category_id FROM ";
                        $getCategory .= $resource->getTableName(catalog_category_product);
                        $getCategory .= " WHERE category_id = 241 AND product_id = $entityId";

                        $categoryId = $readConnection->fetchOne($getCategory);

                        // Caso o produto seja da categoria Purina e a quantidade no stock seja 1 ou mais ele exibe o produto
                        if (($categoryId == true) && ($qty > 0)): $visibility = 4; else: $visibility = 1; endif;
                    }

                    // Verifica se já existe uma visibilidade definida para o produto e cadastra ou atualiza
                    $getVisibilty = "SELECT `value` FROM ";
                    $getVisibilty .= $resource->getTableName(catalog_product_entity_int);
                    $getVisibilty .= " WHERE attribute_id = 102 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                    $resultVisibility = $readConnection->fetchOne($getVisibilty);

                    if (!empty($resultVisibility)) {
                        if ($resultVisibility != $visibility) {
                            // A visibilidade informada pelo CSV é diferente da visibilidade cadastrada no sitema
                            $updateProductVisibility = "UPDATE ";
                            $updateProductVisibility .= $resource->getTableName(catalog_product_entity_int);
                            $updateProductVisibility .= " SET `value` = $visibility";
                            $updateProductVisibility .= " WHERE attribute_id = 102 AND store_id = $tmpStoreView AND `value` = $resultVisibility AND entity_id = $entityId;";

                            $writeConnection->query($updateProductVisibility);
                        }
                    } else {
                        // Não existe visibilidade definida então vamos adicionar uma
                        $setVisibility = "INSERT INTO ";
                        $setVisibility .= $resource->getTableName(catalog_product_entity_int);
                        $setVisibility .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setVisibility .= " VALUES(4, 102, $tmpStoreView, $entityId, $visibility);";

                        $writeConnection->query($setVisibility);
                    }

                    // Verifica se já existe um status definido para o produto e cadastra ou atualiza
                    $getStatus = "SELECT `value` FROM ";
                    $getStatus .= $resource->getTableName(catalog_product_entity_int);
                    $getStatus .= " WHERE attribute_id = 96 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                    $resultStatus = $readConnection->fetchOne($getStatus);

                    if ($resultStatus === false) {
                        echo $status;

                        // Não existe status definido então vamos adicionar um
                        $setStatus = "INSERT INTO ";
                        $setStatus .= $resource->getTableName(catalog_product_entity_int);
                        $setStatus .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setStatus .= " VALUES(4, 96, $tmpStoreView, $entityId, $status);";

                        $writeConnection->query($setStatus);

                    } elseif ($resultStatus != $status) {

                        // O status informado pelo CSV é diferente do status cadastrado no sitema
                        $updateProductStatus = "UPDATE ";
                        $updateProductStatus .= $resource->getTableName(catalog_product_entity_int);
                        $updateProductStatus .= " SET `value` = $status";
                        $updateProductStatus .= " WHERE attribute_id = 96 AND store_id = $tmpStoreView";
                        $updateProductStatus .= " AND `value` = $resultStatus AND entity_id = $entityId;";

                        $writeConnection->query($updateProductStatus);
                    }

                    $ln ++;
                }

                if (!empty($entityId)) {
                    $getProductWebSite = "SELECT website_id FROM ";
                    $getProductWebSite .= $resource->getTableName(catalog_product_website);
                    $getProductWebSite .= " WHERE product_id = $entityId AND website_id = $websiteId;";

                    $productWebSiteId = $readConnection->fetchOne($getProductWebSite);

                    if (empty($productWebSiteId)){
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

                if (empty($resultNameStore)) {
                    // Result name vazio então procura no whitelabel se esse nome do produto está cadastrado
                    $getName = "SELECT `value` FROM ";
                    $getName .= $resource->getTableName(catalog_product_entity_varchar);
                    $getName .= " WHERE attribute_id = 71 AND store_id = 0 AND entity_id = $entityId;";

                    $resultNameWhitelabel = $readConnection->fetchOne($getName);
                }

                if (!empty($resultNameStore) || !empty($resultNameWhitelabel)) {
                    if (($resultNameStore != $name) && !empty($resultNameStore)) {
                        // O nome do produto informado pelo CSV é diferente da descrição cadastrada no sitema
                        $updateProductName = "UPDATE ";
                        $updateProductName .= $resource->getTableName(catalog_product_entity_varchar);
                        $updateProductName .= " SET `value` = '$name'";
                        $updateProductName .= " WHERE attribute_id = 71 AND store_id = $storeId AND `value` = '$resultNameStore';";

                        $writeConnection->query($updateProductName);
                    } elseif (empty($resultNameStore)) {
                        // Não existe um nome definido então vamos adicionar um no whitelabel
                        $setName = "INSERT INTO ";
                        $setName .= $resource->getTableName(catalog_product_entity_varchar);
                        $setName .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setName .= " VALUES(4, 71, $storeId, $entityId, '$name');";

                        $writeConnection->query($setName);
                    }

                } else {
                    // Não existe um nome definido então vamos adicionar um no whitelabel
                    $setName = "INSERT INTO ";
                    $setName .= $resource->getTableName(catalog_product_entity_varchar);
                    $setName .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                    $setName .= " VALUES(4, 71, 0, $entityId, '$name');";

                    $writeConnection->query($setName);
                }

                // Atualiza Stock

                echo ">>>>> stock ID: $stockId$";
                $ln = 1;
                while ($ln <= $iStockId) {
                    $tmpStockId = $arrayStockId[$ln - 1];

                    echo "\n Trabalhando para o Stock ID: $tmpStockId.\n";

                $getStock = "SELECT qty FROM ";
                $getStock .= $resource->getTableName(cataloginventory_stock_item);
                    $getStock .= " WHERE stock_id = $tmpStockId AND product_id = $entityId;";

                $resultQtyStock = $readConnection->fetchOne($getStock);

                if (!empty($resultQtyStock)) {
                        if ($resultQtyStock != $qty) {
                        // A quantidade informada pelo CSV é diferente da quantidade cadastrada no sitema
                        $updateQty = "UPDATE ";
                        $updateQty .= $resource->getTableName(cataloginventory_stock_item);
                        $updateQty .= " SET qty = $qty, is_in_stock = $disp";
                            $updateQty .= " WHERE stock_id = $tmpStockId AND product_id = $entityId;";

                        $writeConnection->query($updateQty);
                    }
                } else {
                    // Não existe um Stock para o produto definido então vamos adicionar um
                    $setStock = "INSERT INTO ";
                    $setStock .= $resource->getTableName(cataloginventory_stock_item);
                    $setStock .= " (product_id, stock_id, qty, min_qty, use_config_min_qty, is_qty_decimal, backorders, use_config_backorders, min_sale_qty, use_config_min_sale_qty, max_sale_qty, use_config_max_sale_qty, is_in_stock, low_stock_date, notify_stock_qty, use_config_notify_stock_qty, manage_stock, use_config_manage_stock, stock_status_changed_auto, use_config_qty_increments, qty_increments, use_config_enable_qty_inc, enable_qty_increments, is_decimal_divided)";
                        $setStock .= " VALUES($entityId, $tmpStockId, $qty, 0, 1, 0, 0, 1, 1.0000, 1, 0, 1, $disp, NULL, NULL, 1, 1, 0, 0, 1, 0.0000, 1, 0, 0);";

                    $writeConnection->query($setStock);
                    }

                    // Define o stock padrão como 0
                    $updateQty = "UPDATE ";
                    $updateQty .= $resource->getTableName(cataloginventory_stock_item);
                    $updateQty .= " SET qty = 0, is_in_stock = $disp";
                    $updateQty .= " WHERE stock_id = 1 AND product_id = $entityId;";

                    $writeConnection->query($updateQty);

                    $ln ++;
                }
            }
        }
        echo "Linha => $i \n";
        $i++;
    }
}