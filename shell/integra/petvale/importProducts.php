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

// Monta o cabeçalho do arquivo
$statusAlteradoHead = "ID Integra|ID ERP|Nome|Store|Antes|Agora\n";
$visibilidadeAlteradaHead = "ID Integra|ID ERP|Nome|Store|Antes|Agora\n";
$newProductsHead = "ID ERP|Nome|stock\n";
$totalProdCategoryHead = "Categoria|Total\n";

$i = 0;
if (empty($lines)) {
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
    foreach ($lines as $key => $value) {
        $i++;
        echo "Linha => $i \n";

        $temp = str_getcsv($value, '|', "'");
        $idErp = $temp[0];
        $name = $temp[1];
        $status = $temp[2];
        $qty = intval($temp[3]);
        $disp = $temp[4];

        // Define a quantidade disponível no estoque do cliente
        if ($qty <= 0): $disp = 0;
        else: $disp = 1;
        endif;

        // Procura o produto pelo id ERP
        $getEntityId = "SELECT entity_id FROM ";
        $getEntityId .= $resource->getTableName(catalog_product_entity_varchar);
        $getEntityId .= " WHERE attribute_id = 185 AND `value` = '$idErp' AND store_id IN ($storeViewAll);";

        $entityId = $readConnection->fetchOne($getEntityId);

        if (in_array($idErp, $ignoreIdErp)) {
            echo "\n\n ID ERP ignorado na variável \$ignoreIdErp\n\n";
        } else {
            if (empty($entityId)) {

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

                    // Define stock do produto
                    $setStock = "INSERT INTO ";
                    $setStock .= $resource->getTableName(cataloginventory_stock_item);
                    $setStock .= " (product_id, stock_id, qty, min_qty, use_config_min_qty, is_qty_decimal, backorders, use_config_backorders, min_sale_qty, use_config_min_sale_qty, max_sale_qty, use_config_max_sale_qty, is_in_stock, low_stock_date, notify_stock_qty, use_config_notify_stock_qty, manage_stock, use_config_manage_stock, stock_status_changed_auto, use_config_qty_increments, qty_increments, use_config_enable_qty_inc, enable_qty_increments, is_decimal_divided)";
                    $setStock .= " VALUES($entityId, $stockId, $qty, 0, 1, 0, 0, 1, 1.0000, 1, 0, 1, $disp, NULL, NULL, 1, 1, 0, 0, 1, 0.0000, 1, 0, 0);";

                    $writeConnection->query($setStock);

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
                    $newProducts .= "$idErp|$name|$qty\n";
                }
            } else {
                // Verifica se já existe um idErp do produto definido para o produto e cadastra ou atualiza
                $arrayStoreView = explode(',', $storeView);
                $iStoreView = count($arrayStoreView);

                $ln = 1;
                while ($ln <= $iStoreView) {
                    $tmpStoreView = $arrayStoreView[$ln - 1];

                    // Pega a categoria do produto que irá definir a visibilidade na store view:
                    // Configura a visibilidade dos produtos nas stores views
                    // Define a visibilidade para todos os produtos na storeView
                    if ($tmpStoreView == 11) {
                        // Disponibiliza todos os produtos para visão principal da petvale (11), com exceção da produtos Vets e Criadores
                        // E Qty > 1
                        $getCategory = "SELECT category_id FROM ";
                        $getCategory .= $resource->getTableName(catalog_category_product);
                        $getCategory .= " WHERE category_id = 545 AND product_id = $entityId";

                        $categoryId = $readConnection->fetchOne($getCategory);
                        if ((($categoryId == true) && ($qty > 0))):
                            if (in_array($idErp, array_merge($idErpCriador,$idErpVets))):
                                $visibility = 1;
                            else:
                                $visibility = 4;
                            endif;

                        else:
                            
                            $visibility = 1;

                        endif;
                    }

                    if ($tmpStoreView == 12) {

                        // A store view é 12 MSD Petvale, o produto está cadastrado na categoria MSD (547) e não é Vet
                        // então visibilidade = catalog, busca (4)
                        $getCategory = "SELECT category_id FROM ";
                        $getCategory .= $resource->getTableName(catalog_category_product);
                        $getCategory .= " WHERE category_id = 547 AND product_id = $entityId";

                        $categoryId = $readConnection->fetchOne($getCategory);

                        // Caso o produto seja da categoria MSD e a quantidade no stock seja 1 ou mais ele exibe o produto
                        if (($categoryId == true) && ($qty > 0)):
                            if (!in_array($idErp, $idErpVets)):
                                $visibility = 4;
                            endif;

                        else:
                            $visibility = 1;
                        endif;
                    }

                    if ($tmpStoreView == 21) {

                        // A store view é 21 MSD Petvale Vets, o produto está cadastrado na categoria MSD (547)
                        // então visibilidade = catalog, busca (4)
                        $getCategory = "SELECT category_id FROM ";
                        $getCategory .= $resource->getTableName(catalog_category_product);
                        $getCategory .= " WHERE category_id = 547 AND product_id = $entityId";

                        $categoryId = $readConnection->fetchOne($getCategory);

                        // Caso o produto seja da categoria MSD e a quantidade no stock seja 1 ou mais ele exibe o produto
                        if (($categoryId == true) && ($qty > 0)):
                            $visibility = 4;
                        else:
                            $visibility = 1;
                        endif;
                    }

                    if ($tmpStoreView == 13) {

                        // A store view é 13 Petvale Vets (visão dos veterinários) 
                        // disponibilizar todos os produtos com excessão dos produtos da visão criadores (14) $idErpCriador
                        $getCategory = "SELECT category_id FROM ";
                        $getCategory .= $resource->getTableName(catalog_category_product);
                        $getCategory .= " WHERE category_id IN (547,597,601,564,607) AND product_id = $entityId";

                        $categoryId = $readConnection->fetchOne($getCategory);

                        if (($categoryId == true) && ($qty > 0)):
                            if (!in_array($idErp) || (in_array($idErpCriador))):
                                $visibility = 4;
                            endif;
                        else:
                            $visibility = 1;
                        endif;
                    }

                    if ($tmpStoreView == 14) {

                        // A store view é 14 PetVale Criador disponibiliza apenas os produtos do Array $idErpCriador + Produtos MSD - $idErpVets
                        // visibilidade = catalog busca (4) caso tenha em estoque
                        $getCategory = "SELECT category_id FROM ";
                        $getCategory .= $resource->getTableName(catalog_category_product);
                        $getCategory .= " WHERE category_id IN (547, 607) AND product_id = $entityId";

                        $categoryId = $readConnection->fetchOne($getCategory);

                        // Caso o produto seja da categoria MSD e a quantidade no stock seja 1 ou mais ele exibe o produto
                        if (($categoryId == true) && ($qty > 0)):
                            if (in_array($idErp, $idErpCriador)):
                                $visibility = 4;
                            
                            elseif (in_array($idErp, $idErpVets)):
                                $visibility = 1;
                            
                            else:
                                $visibility = 4;
                            
                            endif;
                        else:

                            $visibility = 1;
                        endif;
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
                            $visibilidadeAlterada .= "$entityId|$idErp|$name|$tmpStoreView|$resultVisibility|$status\n";
                        }
                    } else {
                        // Não existe visibilidade definida então vamos adicionar uma
                        $setVisibility = "INSERT INTO ";
                        $setVisibility .= $resource->getTableName(catalog_product_entity_int);
                        $setVisibility .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setVisibility .= " VALUES(4, 102, $tmpStoreView, $entityId, $visibility);";

                        $writeConnection->query($setVisibility);
                    }

                    echo "\n\n Trabalhando para o Store ID: $tmpStoreView.\n\n Visibilidade: $visibility\n\n";
                    // Define o idErp para o produto
                    $getIdErp = "SELECT `value` FROM ";
                    $getIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                    $getIdErp .= " WHERE attribute_id = 185 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                    $resultIdErp = $readConnection->fetchOne($getIdErp);

                    if (!empty($resultIdErp)) {
                        if ($resultIdErp != $idErp) {
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

                    // Verifica se já existe um nome definido para o produto na store e cadastra um ou atualiza
                    $getName = "SELECT `value` FROM ";
                    $getName .= $resource->getTableName(catalog_product_entity_varchar);
                    $getName .= " WHERE attribute_id = 71 AND store_id = $tmpStoreView AND entity_id = $entityId;";

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
                            $updateProductName .= " WHERE attribute_id = 71 AND store_id = $tmpStoreView AND `value` = '$resultNameStore';";

                            $writeConnection->query($updateProductName);
                        } elseif (empty($resultNameStore)) {
                            // Não existe um nome definido então vamos adicionar um no whitelabel
                            $setName = "INSERT INTO ";
                            $setName .= $resource->getTableName(catalog_product_entity_varchar);
                            $setName .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setName .= " VALUES(4, 71, $tmpStoreView, $entityId, '$name');";

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










                    // Verifica se já existe um status definido para o produto e cadastra ou atualiza
                    $getStatus = "SELECT `value` FROM ";
                    $getStatus .= $resource->getTableName(catalog_product_entity_int);
                    $getStatus .= " WHERE attribute_id = 96 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                    $resultStatus = $readConnection->fetchOne($getStatus);

                    if ($resultStatus === false) {

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

                        $statusAlterado .= "$entityId|$idErp|$name|$tmpStoreView|$resultStatus|$status\n";
                    }

                    $ln ++;
                }

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
                            $updateQty .= " SET qty = $qty, is_in_stock = $disp, manage_stock = 1, use_config_manage_stock = 0";
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
    }

    // pega aquantidade de produtos dentro de cada categoria da distribuidora
    $sqlRootCategory = "SELECT root_category_id FROM core_store_group WHERE website_id = $websiteId";
    $rootCategory = $readConnection->fetchOne($sqlRootCategory);

    $sqlCategories = "SELECT entity_id FROM catalog_category_entity WHERE path LIKE '1/$rootCategory%' ORDER BY entity_id;";

    $idCategories = $readConnection->fetchAll($sqlCategories);

    $i = 0;
    foreach ($idCategories as $idCategory) {
        $i++;
        $idCategory = $idCategory['entity_id'];
        $getTotalProduct = "SELECT ccev.`value`, COUNT(*) as total FROM catalog_category_product AS ccp
                            INNER JOIN catalog_category_entity_varchar AS ccev
                            ON (ccp.category_id = ccev.entity_id) AND attribute_id = 41
                            WHERE ccp.category_id = $idCategory";

        $arrResult = $readConnection->fetchAll($getTotalProduct);

        foreach ($arrResult as $result) {
            $cat = $result['value'];
            $total = $result['total'];

            if ($cat && $total != 0):
                $totalProdCategory .= "$cat|$total\n";
            endif;
        }
    }

    mkdir("$directoryRep/products/$currentDate", 0777, true);

    // Gera o arquivo de produtos com status alterados
    if (!is_null($statusAlterado)) {
        $outFileStatus = $statusAlteradoHead . $statusAlterado;
        file_put_contents("$directoryRep/products/$currentDate/produtosStatus.csv", $outFileStatus);
        echo "\n Arquivo de produtos com status alterado foi criado.\n";
    }

    // Gera o arquivo de produtos com visibilidade alterados
    if (!is_null($visibilidadeAlterada)) {
        $outFileStatus = $visibilidadeAlteradaHead . $visibilidadeAlterada;
        file_put_contents("$directoryRep/products/$currentDate/produtosVisibilidade.csv", $outFileStatus);
        echo "\n Arquivo de produtos com visibilidade alterada foi criado.\n";
    }

    // Gera o arquivo de produtos que não foram adicionados no integraVets
    if (!is_null($newProducts)) {
        $outFileNewProducts = $newProductsHead . $newProducts;
        file_put_contents("$directoryRep/products/$currentDate/produtosNaoCadastrados.csv", $outFileNewProducts);
        echo "\n Arquivo de produtos que não foram adicionados no integraVets.\n";
    }

    // Gera o arquivo de total de produtos dentro de uma categoria
    if (!is_null($totalProdCategory)) {
        $outFileTotalProducts = $totalProdCategoryHead . $totalProdCategory;
        file_put_contents("$directoryRep/products/$currentDate/produtosNaCategoria.csv", $outFileTotalProducts);
        echo "\n Arquivo de total de produtos dentro de uma categoria.\n";
    }
}