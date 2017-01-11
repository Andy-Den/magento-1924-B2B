<?php

require_once __DIR__ . '/configIntegra.php';

/**
 * Verifica se existe produto cadastrado na categoria corrigir
 * caso exista remove todos.
 */
/*$resultProductsCorrigir = " SELECT COUNT(product_id) FROM ";
$resultProductsCorrigir .= $resource->getTableName(catalog_category_product);
$resultProductsCorrigir .= " where category_id = $idCategoryCorrigir;";

$resultProductsCorrigir = $readConnection->fetchOne($resultProductsCorrigir);

if ($resultProductsCorrigir != 0) {
    $removeProductsCorrigir = "DELETE FROM ";
    $removeProductsCorrigir .= $resource->getTableName(catalog_category_product);
    $removeProductsCorrigir .= " WHERE category_id=$idCategoryCorrigir;";

    $writeConnection->query($removeProductsCorrigir);
}*/

$neededFields = array(
    "store",
    "websites",
    "attribute_set",
    "visibility",
    "type",
    "categories",
    "sku",
    "name",
    "image",
    "small_image",
    "thumbnail",
    "packing",
    "animal",
    "manufacturer",
    "brand",
    "status",
    "description",
    "short_description",
    "enable_qty_increments",
    "use_config_enable_qty_increments",
    "product_name",
    "store_id",
    "product_type_id",
    "product_type",
    "animal_size",
    "animal_age",
    "where_to_use",
    "administer",
    "epi");

//lendo todos os arquivos do diretório
if ($handle = opendir($directoryImp)) {
    /* Esta é a forma correta de varrer o diretório */
    while (FALSE !== ($file = readdir($handle))) {
        if ($handleCsv = fopen("$directoryImp/" . $file, "r")) {
            echo $file . "\n";
            $arrayAux = array();
            $i = 1;
            while (($data = fgetcsv($handleCsv, 0, "|")) !== FALSE) {
                if ($i == 1) {
                    $i++;
                    $arrayAux = array();
                    foreach ($data as $key => $field) {
                        $arrayAux[$field] = $key;
                    }
                    $erros = 0;
                    foreach ($neededFields as $neededField) {
                        if (!array_key_exists($neededField, $arrayAux)) {
                            echo("Corrija o arquivo \"" . $file . "! Está faltando a coluna " . $neededField . "\n");
                            $erros++;
                        }
                    }

                    if ($erros) {
                        die();
                    }

                    continue;
                }
                //$temp = str_getcsv($value, '|', "'");

                $storeCode = $data[$arrayAux['store']];
                $webSite = $data[$arrayAux['websites']];
                $attributeSet = $data[$arrayAux['attribute_set']];
                $visibility = $data[$arrayAux['visibility']];
                $type = $data[$arrayAux['type']];
                $categories = $data[$arrayAux['categories']];
                $sku = $data[$arrayAux['sku']];
                $idErp = $data[$arrayAux['id_erp']];
                $name = addslashes($data[$arrayAux['name']]);
                $image = $data[$arrayAux['image']];
                $smallImage = $data[$arrayAux['small_image']];
                $thumbnail = $data[$arrayAux['thumbnail']];
                $packing = addslashes($data[$arrayAux['packing']]);
                $animal = addslashes($data[$arrayAux['animal']]);
                $manufacture = addslashes($data[$arrayAux['manufacturer']]);
                $status = $data[$arrayAux['status']];
                $description = addslashes(str_replace("\n", "<br>", $data[$arrayAux['description']]));
                $shortDescription = addslashes($data[$arrayAux['short_description']]);
                $productType = $data[$arrayAux['product_type']];
                $animalSize = $data[$arrayAux['animal_size']];
                $animalAge = $data[$arrayAux['animal_age']];
                $whereToUse = addslashes($data[$arrayAux['where_to_use']]);
                $administer = addslashes($data[$arrayAux['administer']]);
                $epi = addslashes($data[$arrayAux['epi']]);
                $brand = addslashes($data[$arrayAux['brand']]);
                if (!$brand) {
                    $brand = $manufacture;
                }

                $arrayCategories = getCategoriesIds($categories);

                if (!$sku || $sku == '') {
                    continue;
                }

                /** Levando em conta que todo produto que está na lista é um produto novo faremos a verificação básica dicionando * */
                // o produto na categoria corrigir apenas caso o SKU seja igual ao de um produto já cadastrado
                // Verifica se o SKU global informado já existe

                $getSKUIntegra = "SELECT entity_id FROM ";
                $getSKUIntegra .= $resource->getTableName(catalog_product_entity);
                $getSKUIntegra .= " WHERE sku = '$sku';";

                $entityId = $readConnection->fetchOne($getSKUIntegra);

                if (empty($entityId)) {
                    // Pega o ID do grupo de atributo
                    $getIdGroupAttribute = "SELECT attribute_set_id FROM ";
                    $getIdGroupAttribute .= $resource->getTableName(eav_attribute_set);
                    $getIdGroupAttribute .= " WHERE attribute_set_name = '$attributeSet';";

                    $idGroupAttribute = $readConnection->fetchOne($getIdGroupAttribute);

                    // Vamos criar o produto na tabela entity
                    $setEntity = "INSERT INTO ";
                    $setEntity .= $resource->getTableName(catalog_product_entity);
                    $setEntity .= " (entity_type_id, attribute_set_id, type_id, sku, has_options, required_options, created_at)";
                    $setEntity .= " VALUES(4, $idGroupAttribute, '$type', '$sku', 0, 0, STR_TO_DATE('$currentDateFormated','%Y-%m-%d %H:%i:%s'));";

                    $writeConnection->query($setEntity);

                    // Verifica se o producto foi criado, caso tenha sido pega o entity_id do produto
                    $getSKUIntegra = "SELECT entity_id FROM ";
                    $getSKUIntegra .= $resource->getTableName(catalog_product_entity);
                    $getSKUIntegra .= " WHERE sku = '$sku';";

                    $entityId = $readConnection->fetchOne($getSKUIntegra);
                }

                if (!empty($entityId)) {
                    // Laço de repetição para pegar os website ids
                    $ln = 1;
                    while ($ln <= $iWebsiteId) {
                        $tmpWebSiteId = $arrayWebSites[$ln - 1];
                        $getProductWebSite = "SELECT website_id FROM ";
                        $getProductWebSite .= $resource->getTableName(catalog_product_website);
                        $getProductWebSite .= " WHERE product_id = $entityId AND website_id = $tmpWebSiteId;";

                        $productWebSiteId = $readConnection->fetchOne($getProductWebSite);

                        if (empty($productWebSiteId)) {
                            $setProductWebSite = "INSERT INTO ";
                            $setProductWebSite .= $resource->getTableName(catalog_product_website);
                            $setProductWebSite .= " (product_id, website_id)";
                            $setProductWebSite .= " VALUES($entityId, $tmpWebSiteId);";

                            $writeConnection->query($setProductWebSite);
                        }

                        $ln++;
                    }

                    // Vamos criar os atributos do produto
                    // Pega o id do Manufacture
                    $getManufacture = "SELECT option_id FROM ";
                    $getManufacture .= $resource->getTableName(eav_attribute_option_value);
                    $getManufacture .= " WHERE value = '$manufacture';";

                    $optionId = $readConnection->fetchOne($getManufacture);

                    if (empty($optionId)) {
                        // Adiciona a manufacture option
                        $setManufactureOption = "INSERT INTO ";
                        $setManufactureOption .= $resource->getTableName(eav_attribute_option);
                        $setManufactureOption .= " (attribute_id, sort_order)";
                        $setManufactureOption .= " VALUES(81, 0);";

                        $writeConnection->query($setManufactureOption);

                        // Pega o option_id para adicionar o value
                        $getManufactureOption = "SELECT option_id FROM ";
                        $getManufactureOption .= $resource->getTableName(eav_attribute_option);
                        $getManufactureOption .= " ORDER BY option_id DESC LIMIT 1;";

                        $manufactureIdOption = $readConnection->fetchOne($getManufactureOption);

                        // Adiciona o manufacture
                        $setManufactureValue = "INSERT INTO ";
                        $setManufactureValue .= $resource->getTableName(eav_attribute_option_value);
                        $setManufactureValue .= " (option_id, store_id, `value`)";
                        $setManufactureValue .= " VALUES($manufactureIdOption, $storeId, '$manufacture');";

                        $writeConnection->query($setManufactureValue);

                        // Pega o ID do manufacture adicionado
                        $getManufacture = "SELECT option_id FROM ";
                        $getManufacture .= $resource->getTableName(eav_attribute_option_value);
                        $getManufacture .= " WHERE value = '$manufacture';";

                        $optionId = $readConnection->fetchOne($getManufacture);
                    }

                    // Verifica se já existe para o produto um manufacture cadastrado ou se precisa atualziar
                    $getProductManufacture = "SELECT `value` FROM ";
                    $getProductManufacture .= $resource->getTableName(catalog_product_entity_int);
                    $getProductManufacture .= " WHERE attribute_id = 81 AND entity_id = $entityId;";

                    $resultManufacture = $readConnection->fetchOne($getProductManufacture);

                    if ((!empty($resultManufacture)) || (is_null($resultManufacture))) {
                        if ($resultManufacture != $optionId) {
                            // Manufacture informada pelo CSV é diferente da cadastrada então atualiza
                            $updateProductManufacture = "UPDATE ";
                            $updateProductManufacture .= $resource->getTableName(catalog_product_entity_int);
                            $updateProductManufacture .= " SET `value` = $optionId";
                            $updateProductManufacture .= " WHERE attribute_id = 81 AND store_id = $storeId AND entity_id = $entityId;";

                            $writeConnection->query($updateProductManufacture);
                        }
                    } else {
                        // não existe manufacture cadastrada então adiciona
                        // Associa a manufacture correspondente ao produto
                        $setManufactureProduct = "INSERT INTO ";
                        $setManufactureProduct .= $resource->getTableName(catalog_product_entity_int);
                        $setManufactureProduct .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setManufactureProduct .= " VALUES(4, 81, $storeId, $entityId, $optionId);";

                        $writeConnection->query($setManufactureProduct);
                    }

                    // Seta o produto como desabilitado para o whitelabel caso o storeId seja diferente ele pega o status
                    // enviado pelo arquivo csv
                    // Verifica se já existe um status definido para o produto e cria ou atualiza
                    if ($storeId == 0) {
                        $tmpStatus = 2;
                    } else {
                        $tmpStatus = $status;
                    }

                    $getStatus = "SELECT `value` FROM ";
                    $getStatus .= $resource->getTableName(catalog_product_entity_int);
                    $getStatus .= " WHERE attribute_id = 96 AND store_id = $storeId AND entity_id = $entityId;";

                    $resultStatus = $readConnection->fetchOne($getStatus);

                    if (!empty($resultStatus)) {
                        if ($resultStatus != $tmpStatus && trim($tmpStatus) != '') {

                            // O status informado pelo CSV é diferente do status cadastrado no sitema
                            $updateProductStatus = "UPDATE ";
                            $updateProductStatus .= $resource->getTableName(catalog_product_entity_int);
                            $updateProductStatus .= " SET `value` = $tmpStatus";
                            $updateProductStatus .= " WHERE attribute_id = 96 AND store_id = $storeId AND entity_id = $entityId;";

                            $writeConnection->query($updateProductStatus);
                        }
                    } else {
                        // Não existe status definido então vamos adicionar um
                        $setStatus = "INSERT INTO ";
                        $setStatus .= $resource->getTableName(catalog_product_entity_int);
                        $setStatus .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setStatus .= " VALUES(4, 96, $storeId, $entityId, $tmpStatus);";

                        $writeConnection->query($setStatus);
                    }

                    // Define a visibilidade do produto caso seja whitelabel seta para não exibir individualmente
                    if ($storeId == 0) {
                        $tmpVisibility = 1;
                    } else {
                        $tmpVisibility = $visibility;
                    }
                    // Verifica se já existe uma visibilidade definida para o produto e cadastra ou atualiza
                    $getVisibilty = "SELECT `value` FROM ";
                    $getVisibilty .= $resource->getTableName(catalog_product_entity_int);
                    $getVisibilty .= " WHERE attribute_id = 102 AND store_id = $storeId AND entity_id = $entityId;";

                    $resultVisibility = $readConnection->fetchOne($getVisibilty);

                    if (!empty($resultVisibility)) {
                        if ($resultVisibility != $tmpVisibility) {
                            // A visibilidade informada pelo CSV é diferente da visibilidade cadastrada no sitema
                            $updateProductVisibility = "UPDATE ";
                            $updateProductVisibility .= $resource->getTableName(catalog_product_entity_int);
                            $updateProductVisibility .= " SET `value` = $tmpVisibility";
                            $updateProductVisibility .= " WHERE attribute_id = 102 AND store_id = $storeId AND entity_id = $entityId;";

                            $writeConnection->query($updateProductVisibility);
                        }
                    } else {
                        // Não existe visibilidade definida então vamos adicionar uma
                        $setVisibility = "INSERT INTO ";
                        $setVisibility .= $resource->getTableName(catalog_product_entity_int);
                        $setVisibility .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setVisibility .= " VALUE(4, 102, $storeId, $entityId, $tmpVisibility);";

                        $writeConnection->query($setVisibility);
                    }


                    /*                     * ****
                     * // Remover depois dessa execução
                     * ***** */
                    // Monta a URl do produto, caso não seja enviado pelo csv:
                    if (is_null($url)) {
                        $url = clean($name);
                    }

                    $arrayStoreTemps = array_merge($defaultStore, $storeView);

                    $iStoreTemps = count($storeTemps);

                    // Laço de repetição para pegar os ids equivalentes aos animais
                    // cada ID é atribuido ao array de animais para ser convertido novamente em string
                    $ln = 1;
                    while ($ln <= $iStoreTemps) {
                        $storeTemp = $arrayStoreTemps[$ln - 1];
                        // Verifica se já existe uma URL definido para o produto e cadastra ou atualiza
                        $getUrl = "SELECT `value` FROM ";
                        $getUrl .= $resource->getTableName(catalog_product_entity_varchar);
                        $getUrl .= " WHERE attribute_id = 97 AND store_id = $storeTemp AND entity_id = $entityId;";

                        $resultUrl = $readConnection->fetchOne($getUrl);

                        if (!empty($resultUrl)) {
                            if ($resultUrl != $url) {
                                // A URL informada pelo CSV é diferente da descrição cadastrada no sitema
                                $updateProductUrl = "UPDATE ";
                                $updateProductUrl .= $resource->getTableName(catalog_product_entity_varchar);
                                $updateProductUrl .= " SET `value` = '$url'";
                                $updateProductUrl .= " WHERE attribute_id = 97 AND store_id = $storeTemp AND entity_id = $entityId;";

                                $writeConnection->query($updateProductUrl);
                            }
                        } else {
                            // Não existe uma URL  definida então vamos adicionar uma
                            $setUrl = "INSERT INTO ";
                            $setUrl .= $resource->getTableName(catalog_product_entity_varchar);
                            $setUrl .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setUrl .= " VALUE(4, 97, $storeTemp, $entityId, '$url');";

                            $writeConnection->query($setUrl);
                        }

                        // Monta a URl Path do produto, caso não seja enviado pelo csv:
                        if (is_null($urlPath)) {
                            $urlPath = clean($name) . '.html';
                        }

                        // Verifica se já existe uma url path definido para o produto e cadastra ou atualiza
                        $getUrlPath = "SELECT `value` FROM ";
                        $getUrlPath .= $resource->getTableName(catalog_product_entity_varchar);
                        $getUrlPath .= " WHERE attribute_id = 98 AND store_id = $storeTemp AND entity_id = $entityId;";

                        $resultUrlPath = $readConnection->fetchOne($getUrlPath);

                        if (!empty($resultUrlPath)) {
                            if ($resultUrlPath != $urlPath) {
                                // A url path informada pelo CSV é diferente da descrição cadastrada no sitema
                                $updateProductUrlPath = "UPDATE ";
                                $updateProductUrlPath .= $resource->getTableName(catalog_product_entity_varchar);
                                $updateProductUrlPath .= " SET `value` = '$urlPath'";
                                $updateProductUrlPath .= " WHERE attribute_id = 98 AND store_id = $storeTemp AND entity_id = $entityId;";

                                $writeConnection->query($updateProductUrlPath);
                            }
                        } else {
                            // Não existe uma url path definida então vamos adicionar uma
                            $setUrlPath = "INSERT INTO ";
                            $setUrlPath .= $resource->getTableName(catalog_product_entity_varchar);
                            $setUrlPath .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setUrlPath .= " VALUE(4, 98, $storeTemp, $entityId, '$urlPath');";

                            $writeConnection->query($setUrlPath);
                        }
                        $ln++;
                    }


                    $url = NULL;
                    $urlPath = NULL;

                    // Verifica se já existe uma classe de imposto definida para o produto e cadastra ou atualiza
                    $taxClass = 0; // por padrão
                    $getTaxClass = "SELECT `value` FROM ";
                    $getTaxClass .= $resource->getTableName(catalog_product_entity_int);
                    $getTaxClass .= " WHERE attribute_id = 121 AND store_id = $storeId AND entity_id = $entityId;";

                    $resultTaxClass = $readConnection->fetchOne($getTaxClass);

                    if ($resultTaxClass !== NULL) {
                        if ($resultTaxClass != $taxClass) {
                            // A taxa de imposto informada pelo CSV é diferente da visibilidade cadastrada no sitema
                            $updateProductTaxClass = "UPDATE ";
                            $updateProductTaxClass .= $resource->getTableName(catalog_product_entity_int);
                            $updateProductTaxClass .= " SET `value` = $taxClass";
                            $updateProductTaxClass .= " WHERE attribute_id = 121 AND store_id = $storeId AND entity_id = $entityId;";

                            $writeConnection->query($updateProductTaxClass);
                        }
                    } else {
                        // Não existe classe de imposto definida então vamos adicionar uma
                        $setTaxClass = "INSERT IGNORE INTO ";
                        $setTaxClass .= $resource->getTableName(catalog_product_entity_int);
                        $setTaxClass .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setTaxClass .= " VALUE(4, 121, $storeId, $entityId, $taxClass);";

                        $writeConnection->query($setTaxClass);
                    }

                    if (!empty($description)) {
                        // Verifica se já existe uma descriçao definida para o produto e cadastra ou atualiza
                        $getDescription = "SELECT `value` FROM ";
                        $getDescription .= $resource->getTableName(catalog_product_entity_text);
                        $getDescription .= " WHERE attribute_id = 72 AND store_id = 0 AND entity_id = $entityId;";

                        $resultDescription = $readConnection->fetchOne($getDescription);
                        $resultDescription = addslashes($resultDescription);

                        if (!empty($resultDescription)) {
                            //$resultDescription = str_replace("'", '\'', $resultDescription);
                            if (($resultDescription != $description)) {
                                // A descrição informada pelo CSV é diferente da descrição cadastrada no sitema
                                $updateProductDescription = "UPDATE ";
                                $updateProductDescription .= $resource->getTableName(catalog_product_entity_text);
                                $updateProductDescription .= " SET `value` = '$description'";
                                $updateProductDescription .= " WHERE attribute_id = 72 AND store_id = 0 AND entity_id = $entityId;";

                                $writeConnection->query($updateProductDescription);
                            }
                        } elseif (!empty($description)) {
                            // Não existe uma descrição definida então vamos adicionar uma
                            $setDescription = "INSERT INTO ";
                            $setDescription .= $resource->getTableName(catalog_product_entity_text);
                            $setDescription .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setDescription .= " VALUE(4, 72, 0, $entityId, '$description');";

                            $writeConnection->query($setDescription);
                        }
                    }

                    if (!empty($shortDescription)) {
                        // Verifica se já existe uma descriçao resumida definida para o produto e cadastra ou atualiza
                        $getShortDescription = "SELECT `value` FROM ";
                        $getShortDescription .= $resource->getTableName(catalog_product_entity_text);
                        $getShortDescription .= " WHERE attribute_id = 73 AND store_id = 0 AND entity_id = $entityId;";

                        $resultShortDescription = $readConnection->fetchOne($getShortDescription);

                        if (!empty($resultShortDescription)) {
                            if ($resultShortDescription != $shortDescription) {
                                $resultShortDescription = addslashes($resultShortDescription);

                                // A descrição resumida informada pelo CSV é diferente da descrição cadastrada no sitema
                                $updateProductShortDescription = "UPDATE ";
                                $updateProductShortDescription .= $resource->getTableName(catalog_product_entity_text);
                                $updateProductShortDescription .= " SET `value` = '$shortDescription'";
                                $updateProductShortDescription .= " WHERE attribute_id = 73 AND store_id = 0 AND entity_id = $entityId;";

                                $writeConnection->query($updateProductShortDescription);
                            }
                        } elseif (!empty($shortDescription)) {
                            // Não existe uma descrição resumida definida então vamos adicionar uma
                            $setShortDescription = "INSERT INTO ";
                            $setShortDescription .= $resource->getTableName(catalog_product_entity_text);
                            $setShortDescription .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setShortDescription .= " VALUE(4, 73, 0, $entityId, '$shortDescription');";

                            $writeConnection->query($setShortDescription);
                        }
                    }

                    if ($name){
                        // Verifica se já existe um nome definido para o produto e cadastra ou atualiza
                        $getName = "SELECT `value` FROM ";
                        $getName .= $resource->getTableName(catalog_product_entity_varchar);
                        $getName .= " WHERE attribute_id = 71 AND store_id = $storeId AND entity_id = $entityId;";

                        $resultName = $readConnection->fetchOne($getName);

                        if (!empty($resultName) || $resultName === '') {
                            if ($resultName != $name) {
                                $resultName = addSlashes($resultName);

                                // O nome do produto informado pelo CSV é diferente da descrição cadastrada no sitema
                                $updateProductName = "UPDATE ";
                                $updateProductName .= $resource->getTableName(catalog_product_entity_varchar);
                                $updateProductName .= " SET `value` = '$name'";
                                $updateProductName .= " WHERE attribute_id = 71 AND store_id = $storeId AND entity_id = $entityId;";

                                $writeConnection->query($updateProductName);
                            }
                        } else {
                            // Não existe um nome definido então vamos adicionar uma
                            $setName = "INSERT INTO ";
                            $setName .= $resource->getTableName(catalog_product_entity_varchar);
                            $setName .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setName .= " VALUE(4, 71, $storeId, $entityId, '$name');";

                            $writeConnection->query($setName);
                        }
                    }

                    // Verifica se já existe um tipo de produto definido para o produto e cadastra ou atualiza
                    $getProductType = "SELECT `value` FROM ";
                    $getProductType .= $resource->getTableName(catalog_product_entity_varchar);
                    $getProductType .= " WHERE attribute_id = 162 AND store_id = $storeId AND entity_id = $entityId;";

                    $resultProductType = $readConnection->fetchOne($getProductType);

                    if ($resultProductType == "") {
                        $rmAttributeInvalid = "DELETE FROM ";
                        $rmAttributeInvalid .= $resource->getTableName(catalog_product_entity_varchar);
                        $rmAttributeInvalid .= " WHERE entity_id = $entityId AND attribute_id = 162 AND `value` = \"\";";

                        $writeConnection->query($rmAttributeInvalid);
                    }

                    if (!empty($resultProductType)) {
                        if ($resultProductType != $productType) {
                            // O tipo de produto informada pelo CSV é diferente da descrição cadastrada no sitema
                            $updateProductType = "UPDATE ";
                            $updateProductType .= $resource->getTableName(catalog_product_entity_varchar);
                            $updateProductType .= " SET `value` = '$productType'";
                            $updateProductType .= " WHERE attribute_id = 162 AND store_id = $storeId AND entity_id = $entityId;";

                            $writeConnection->query($updateProductType);
                        }
                    } elseif (!empty($productType)) {
                        // Não existe um tipo de produto definido então vamos adicionar uma
                        $setProductType = "INSERT IGNORE INTO ";
                        $setProductType .= $resource->getTableName(catalog_product_entity_varchar);
                        $setProductType .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setProductType .= " VALUE(4, 162, $storeId, $entityId, '$productType');";

                        $writeConnection->query($setProductType);
                    }

                    if ($whereToUse) {

                        // Pega o id do Where to use
                        $getWhereToUse = "SELECT eaov.option_id FROM ";
                        $getWhereToUse .= $resource->getTableName(eav_attribute_option_value) . " eaov";
                        $getWhereToUse .= " join " . $resource->getTableName(eav_attribute_option) . " eao on eaov.option_id = eao.option_id";
                        $getWhereToUse .= " WHERE  value = '$whereToUse' and attribute_id = 150;";

                        $optionId = $readConnection->fetchOne($getWhereToUse);

                        if (empty($optionId) || !$optionId) {
                            // Adiciona um where to use option
                            $setWhereToUse = "INSERT INTO ";
                            $setWhereToUse .= $resource->getTableName(eav_attribute_option);
                            $setWhereToUse .= " (attribute_id, sort_order)";
                            $setWhereToUse .= " VALUES(162, 0);";

                            $writeConnection->query($setWhereToUse);

                            // Pega o option_id para adicionar o value
                            $getWhereToUseOption = "SELECT option_id FROM ";
                            $getWhereToUseOption .= $resource->getTableName(eav_attribute_option);
                            $getWhereToUseOption .= " ORDER BY option_id DESC LIMIT 1;";

                            $whereToUseIdOption = $readConnection->fetchOne($getWhereToUseOption);

                            // Adiciona o where to use
                            $setWhereToUseValue = "INSERT INTO ";
                            $setWhereToUseValue .= $resource->getTableName(eav_attribute_option_value);
                            $setWhereToUseValue .= " (option_id, store_id, `value`)";
                            $setWhereToUseValue .= " VALUES($whereToUseIdOption, $storeId, '$whereToUse');";

                            $writeConnection->query($setWhereToUseValue);

                            // Pega o ID do where to use adicionado
                            $getWhereToUse = "SELECT option_id FROM ";
                            $getWhereToUse .= $resource->getTableName(eav_attribute_option_value);
                            $getWhereToUse .= " WHERE value = '$whereToUse';";

                            $optionId = $readConnection->fetchOne($getWhereToUse);
                        }

                        // Verifica se já existe para o produto um where to use cadastrado ou se precisa atualziar
                        $getProductWhereToUse = "SELECT `value` FROM ";
                        $getProductWhereToUse .= $resource->getTableName(catalog_product_entity_varchar);
                        $getProductWhereToUse .= " WHERE attribute_id = 150 AND entity_id = $entityId;";

                        $resultWhereToUse = $readConnection->fetchOne($getProductWhereToUse);

                        if (!empty($resultWhereToUse)) {
                            if ($resultWhereToUse != $optionId) {
                                // Manufacture informada pelo CSV é diferente da cadastrada então atualiza
                                $updateProductWhereToUse = "UPDATE ";
                                $updateProductWhereToUse .= $resource->getTableName(catalog_product_entity_varchar);
                                $updateProductWhereToUse .= " SET `value` = $optionId";
                                $updateProductWhereToUse .= " WHERE attribute_id = 150 AND store_id = $storeId AND entity_id = $entityId;";

                                $writeConnection->query($updateProductWhereToUse);
                            }
                        } else {
                            // não existe where to use cadastrada então adiciona
                            // Associa o where to use correspondente ao produto
                            $setWhereToUseProduct = "INSERT ignore INTO ";
                            $setWhereToUseProduct .= $resource->getTableName(catalog_product_entity_varchar);
                            $setWhereToUseProduct .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setWhereToUseProduct .= " VALUES(4, 150, $storeId, $entityId, $optionId);";

                            $writeConnection->query($setWhereToUseProduct);
                        }
                    }
                    // Verifica se já existe um packing do produto definido para o produto e cadastra ou atualiza
                    $getPacking = "SELECT `value` FROM ";
                    $getPacking .= $resource->getTableName(catalog_product_entity_varchar);
                    $getPacking .= " WHERE attribute_id = 166 AND store_id = $storeId AND entity_id = $entityId;";

                    $resultPacking = $readConnection->fetchOne($getPacking);

                    if ($resultPacking == "") {
                        $rmAttributeInvalid = "DELETE FROM ";
                        $rmAttributeInvalid .= $resource->getTableName(catalog_product_entity_varchar);
                        $rmAttributeInvalid .= " WHERE entity_id = $entityId AND attribute_id = 166 AND `value` = \"\";";

                        $writeConnection->query($rmAttributeInvalid);
                    }

                    if (!empty($resultPacking)) {
                        if ($resultPacking != $packing) {
                            // O packing informado pelo CSV é diferente da descrição cadastrada no sitema
                            $updatePacking = "UPDATE ";
                            $updatePacking .= $resource->getTableName(catalog_product_entity_varchar);
                            $updatePacking .= " SET `value` = '$productType'";
                            $updatePacking .= " WHERE attribute_id = 166 AND store_id = $storeId AND entity_id = $entityId;";

                            $writeConnection->query($updatePacking);
                        }
                    } elseif (!empty($packing)) {
                        // Não existe um tipo de produto definido então vamos adicionar uma
                        $setPacking = "INSERT IGNORE INTO ";
                        $setPacking .= $resource->getTableName(catalog_product_entity_varchar);
                        $setPacking .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setPacking .= " VALUE(4, 166, $storeId, $entityId, '$packing');";

                        $writeConnection->query($setPacking);
                    }

                    // Pega o id do animal size
                    $getAnimalSize = "SELECT option_id FROM ";
                    $getAnimalSize .= $resource->getTableName(eav_attribute_option_value);
                    $getAnimalSize .= " WHERE value = '$animalSize';";

                    $optionId = $readConnection->fetchOne($getAnimalSize);

                    if (empty($optionId)) {
                        // Adiciona um animal size option
                        $setAnimalSize = "INSERT INTO ";
                        $setAnimalSize .= $resource->getTableName(eav_attribute_option);
                        $setAnimalSize .= " (attribute_id, sort_order)";
                        $setAnimalSize .= " VALUES(177, 0);";

                        $writeConnection->query($setAnimalSize);

                        // Pega o option_id para adicionar o value
                        $getAnimalSizeOption = "SELECT option_id FROM ";
                        $getAnimalSizeOption .= $resource->getTableName(eav_attribute_option);
                        $getAnimalSizeOption .= " ORDER BY option_id DESC LIMIT 1;";

                        $animalSizeIdOption = $readConnection->fetchOne($getAnimalSizeOption);

                        // Adiciona o animal size
                        $setAnimalSizeValue = "INSERT INTO ";
                        $setAnimalSizeValue .= $resource->getTableName(eav_attribute_option_value);
                        $setAnimalSizeValue .= " (option_id, store_id, `value`)";
                        $setAnimalSizeValue .= " VALUES($animalSizeIdOption, $storeId, '$animalSize');";

                        $writeConnection->query($setAnimalSizeValue);

                        // Pega o ID do animal size adicionado
                        $getAnimalSize = "SELECT option_id FROM ";
                        $getAnimalSize .= $resource->getTableName(eav_attribute_option_value);
                        $getAnimalSize .= " WHERE value = '$animalSize';";

                        $optionId = $readConnection->fetchOne($getAnimalSize);
                    }

                    // Verifica se já existe para o produto um animal size cadastrado ou se precisa atualziar
                    $getProductAnimalSize = "SELECT `value` FROM ";
                    $getProductAnimalSize .= $resource->getTableName(catalog_product_entity_varchar);
                    $getProductAnimalSize .= " WHERE attribute_id = 177 AND entity_id = $entityId;";

                    $resultAnimalSize = $readConnection->fetchOne($getProductAnimalSize);

                    if (!empty($resultAnimalSize)) {
                        if ($resultAnimalSize != $optionId) {
                            // Animal size informada pelo CSV é diferente da cadastrada então atualiza
                            $updateProductAnimalSize = "UPDATE ";
                            $updateProductAnimalSize .= $resource->getTableName(catalog_product_entity_varchar);
                            $updateProductAnimalSize .= " SET `value` = $optionId";
                            $updateProductAnimalSize .= " WHERE attribute_id = 177 AND store_id = $storeId AND entity_id = $entityId;";

                            $writeConnection->query($updateProductAnimalSize);
                        }
                    } else {
                        // não existe animal size cadastrada então adiciona
                        // Associa o animal size correspondente ao produto
                        $setAnimalSizeProduct = "INSERT IGNORE INTO ";
                        $setAnimalSizeProduct .= $resource->getTableName(catalog_product_entity_varchar);
                        $setAnimalSizeProduct .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setAnimalSizeProduct .= " VALUES(4, 177, $storeId, $entityId, $optionId);";

                        $writeConnection->query($setAnimalSizeProduct);
                    }

                    // Pega o id do animal age
                    $getAnimalAge = "SELECT eaov.option_id FROM ";
                    $getAnimalAge .= $resource->getTableName(eav_attribute_option_value) . " as eaov";
                    $getAnimalAge .= " INNER JOIN ";
                    $getAnimalAge .= $resource->getTableName(eav_attribute_option) . " as eao";
                    $getAnimalAge .= " ON eao.option_id = eaov.option_id";
                    $getAnimalAge .= " WHERE eaov.`value` = '$animalAge' AND eao.attribute_id = 178;";

                    $optionId = $readConnection->fetchOne($getAnimalAge);

                    if (empty($optionId)) {
                        // Adiciona um animal age option
                        $setAnimalAge = "INSERT INTO ";
                        $setAnimalAge .= $resource->getTableName(eav_attribute_option);
                        $setAnimalAge .= " (attribute_id, sort_order)";
                        $setAnimalAge .= " VALUES(178, 0);";

                        $writeConnection->query($setAnimalAge);

                        // Pega o option_id para adicionar o value
                        $getAnimalAgeOption = "SELECT option_id FROM ";
                        $getAnimalAgeOption .= $resource->getTableName(eav_attribute_option);
                        $getAnimalAgeOption .= " ORDER BY option_id DESC LIMIT 1;";

                        $animalAgeIdOption = $readConnection->fetchOne($getAnimalAgeOption);

                        // Adiciona o animal age
                        $setAnimalAgeValue = "INSERT INTO ";
                        $setAnimalAgeValue .= $resource->getTableName(eav_attribute_option_value);
                        $setAnimalAgeValue .= " (option_id, store_id, `value`)";
                        $setAnimalAgeValue .= " VALUES($animalAgeIdOption, $storeId, '$animalAge');";

                        $writeConnection->query($setAnimalAgeValue);

                        // Pega o ID do animal age adicionado
                        $getAnimalAge = "SELECT option_id FROM ";
                        $getAnimalAge .= $resource->getTableName(eav_attribute_option_value);
                        $getAnimalAge .= " WHERE value = '$animalAge';";

                        $optionId = $readConnection->fetchOne($getAnimalAge);
                    }

                    if ($animal) {

                        // Verifica se já existe para o produto um animal age cadastrado ou se precisa atualziar
                        $getProductAnimalAge = "SELECT `value` FROM ";
                        $getProductAnimalAge .= $resource->getTableName(catalog_product_entity_varchar);
                        $getProductAnimalAge .= " WHERE attribute_id = 178 AND entity_id = $entityId;";

                        $resultAnimalAge = $readConnection->fetchOne($getProductAnimalAge);

                        if (!empty($resultAnimalAge)) {
                            if ($resultAnimalAge != $optionId) {
                                // Animal age informada pelo CSV é diferente da cadastrada então atualiza
                                $updateProductAnimalAge = "UPDATE ";
                                $updateProductAnimalAge .= $resource->getTableName(catalog_product_entity_varchar);
                                $updateProductAnimalAge .= " SET `value` = $optionId";
                                $updateProductAnimalAge .= " WHERE attribute_id = 178 AND store_id = $storeId AND entity_id = $entityId;";

                                $writeConnection->query($updateProductAnimalAge);
                            }
                        } else {
                            // não existe animal age cadastrada então adiciona
                            // Associa o animal age correspondente ao produto
                            $setAnimalAgeProduct = "INSERT IGNORE INTO ";
                            $setAnimalAgeProduct .= $resource->getTableName(catalog_product_entity_varchar);
                            $setAnimalAgeProduct .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setAnimalAgeProduct .= " VALUES(4, 178, $storeId, $entityId, $optionId);";

                            $writeConnection->query($setAnimalAgeProduct);
                        }

                        // Converte a string em array
                        $arrayAnimal = explode(',', $animal);
                        // Analiza quantos índeces existem
                        $iAnimal = count($arrayAnimal);

                        // Laço de repetição para pegar os ids equivalentes aos animais
                        // cada ID é atribuido ao array de animais para ser convertido novamente em string
                        $ln = 1;
                        while ($ln <= $iAnimal) {

                            $tmpAnimal = $arrayAnimal[$ln - 1];
                            // Pega o id do animal
                            $getAnimal = "SELECT option_id FROM ";
                            $getAnimal .= $resource->getTableName(eav_attribute_option_value);
                            $getAnimal .= " WHERE value = '$tmpAnimal';";

                            $optionId = $readConnection->fetchOne($getAnimal);

                            if (empty($optionId)) {
                                // Adiciona um animal option
                                $setAnimal = "INSERT INTO ";
                                $setAnimal .= $resource->getTableName(eav_attribute_option);
                                $setAnimal .= " (attribute_id, sort_order)";
                                $setAnimal .= " VALUES(179, 0);";

                                $writeConnection->query($setAnimal);

                                // Pega o option_id para adicionar o value
                                $getAnimalOption = "SELECT option_id FROM ";
                                $getAnimalOption .= $resource->getTableName(eav_attribute_option);
                                $getAnimalOption .= " ORDER BY option_id DESC LIMIT 1;";

                                $animalIdOption = $readConnection->fetchOne($getAnimalOption);

                                // Adiciona o animal
                                $setAnimalValue = "INSERT INTO ";
                                $setAnimalValue .= $resource->getTableName(eav_attribute_option_value);
                                $setAnimalValue .= " (option_id, store_id, `value`)";
                                $setAnimalValue .= " VALUES($animalIdOption, $storeId, '$tmpAnimal');";

                                $writeConnection->query($setAnimalValue);

                                // Pega o ID do animal size adicionado
                                $getAnimal = "SELECT option_id FROM ";
                                $getAnimal .= $resource->getTableName(eav_attribute_option_value);
                                $getAnimal .= " WHERE value = '$tmpAnimal';";

                                $optionId = $readConnection->fetchOne($getAnimal);
                            }

                            // Adiciona o optionId equivalente ao animal no array
                            $arrayOptionAnimal[] = $optionId;
                            $ln++;
                        }

                        // Converte o array para string
                        $strAnimal = implode(',', $arrayOptionAnimal);

                        // Verifica se já existe para o produto um animal cadastrado ou se precisa atualziar
                        $getProductAnimal = "SELECT `value` FROM ";
                        $getProductAnimal .= $resource->getTableName(catalog_product_entity_varchar);
                        $getProductAnimal .= " WHERE attribute_id = 179 AND entity_id = $entityId;";

                        $resultAnimal = $readConnection->fetchOne($getProductAnimal);

                        if (!empty($resultAnimal)) {
                            if ($resultAnimal != $strAnimal) {
                                // Animal age informada pelo CSV é diferente da cadastrada então atualiza
                                $updateProductAnimal = "UPDATE ";
                                $updateProductAnimal .= $resource->getTableName(catalog_product_entity_varchar);
                                $updateProductAnimal .= " SET `value` = '$strAnimal'";
                                $updateProductAnimal .= " WHERE attribute_id = 179 AND store_id = $storeId AND entity_id = $entityId;";

                                $writeConnection->query($updateProductAnimal);
                            }
                        } else {
                            // não existe animal cadastrada então adiciona
                            // Associa o animal correspondente ao produto
                            $setAnimalProduct = "INSERT IGNORE INTO ";
                            $setAnimalProduct .= $resource->getTableName(catalog_product_entity_varchar);
                            $setAnimalProduct .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setAnimalProduct .= " VALUES(4, 179, $storeId, $entityId, '$strAnimal');";

                            $writeConnection->query($setAnimalProduct);
                        }
                    }

                    // Pega o id da via de administração
                    $getAdminister = "SELECT eaov.option_id FROM ";
                    $getAdminister .= $resource->getTableName(eav_attribute_option_value) . " as eaov";
                    $getAdminister .= " INNER JOIN ";
                    $getAdminister .= $resource->getTableName(eav_attribute_option) . " as eao";
                    $getAdminister .= " ON eao.option_id = eaov.option_id";
                    $getAdminister .= " WHERE eaov.`value` = '$administer' AND eao.attribute_id = 180 LIMIT 1;";

                    $optionId = $readConnection->fetchOne($getAdminister);

                    if (empty($optionId)) {
                        // Adiciona um animal size option
                        $setAdminister = "INSERT INTO ";
                        $setAdminister .= $resource->getTableName(eav_attribute_option);
                        $setAdminister .= " (attribute_id, sort_order)";
                        $setAdminister .= " VALUES(180, 0);";

                        $writeConnection->query($setAdminister);

                        // Pega o option_id para adicionar o value
                        $getAdministerOption = "SELECT option_id FROM ";
                        $getAdministerOption .= $resource->getTableName(eav_attribute_option);
                        $getAdministerOption .= " ORDER BY option_id DESC LIMIT 1;";

                        $animalAdministerIdOption = $readConnection->fetchOne($getAdministerOption);

                        // Adiciona a forma de administração
                        $setAdministerValue = "INSERT INTO ";
                        $setAdministerValue .= $resource->getTableName(eav_attribute_option_value);
                        $setAdministerValue .= " (option_id, store_id, `value`)";
                        $setAdministerValue .= " VALUES($animalAdministerIdOption, $storeId, '$administer');";

                        $writeConnection->query($setAdministerValue);

                        // Pega o ID da forma de administraçao adicionado
                        $getAdminister = "SELECT eaov.option_id FROM ";
                        $getAdminister .= $resource->getTableName(eav_attribute_option_value) . " as eaov";
                        $getAdminister .= " INNER JOIN ";
                        $getAdminister .= $resource->getTableName(eav_attribute_option) . " as eao";
                        $getAdminister .= " ON eao.option_id = eaov.option_id";
                        $getAdminister .= " WHERE eaov.`value` = '$administer' AND eao.attribute_id = 180;";

                        $optionId = $readConnection->fetchOne($getAdminister);
                    }

                    // Verifica se já existe para o produto uma forma de administração cadastrado ou se precisa atualziar
                    $getProductAdminister = "SELECT `value` FROM ";
                    $getProductAdminister .= $resource->getTableName(catalog_product_entity_int);
                    $getProductAdminister .= " WHERE attribute_id = 180 AND entity_id = $entityId;";

                    $resultAdminister = $readConnection->fetchOne($getProductAdminister);

                    if (!empty($resultAdminister)) {
                        if ($resultAdminiter != $optionId) {
                            // A forma de administração informada pelo CSV é diferente da cadastrada então atualiza
                            $updateProductAdminister = "UPDATE ";
                            $updateProductAdminister .= $resource->getTableName(catalog_product_entity_int);
                            $updateProductAdminister .= " SET `value` = $optionId";
                            $updateProductAdminister .= " WHERE attribute_id = 180 AND store_id = $storeId AND entity_id = $entityId;";

                            $writeConnection->query($updateProductAdminister);
                        }
                    } else {
                        // não existe forma de administração cadastrada então adiciona
                        // Associa a forma de administração correspondente ao produto
                        $setAdministerProduct = "INSERT IGNORE INTO ";
                        $setAdministerProduct .= $resource->getTableName(catalog_product_entity_int);
                        $setAdministerProduct .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                        $setAdministerProduct .= " VALUES(4, 180, $storeId, $entityId, $optionId);";

                        $writeConnection->query($setAdministerProduct);
                    }


                    if ($epi) {
                        // Verifica se já existe um EPI definido para o produto e cadastra ou atualiza
                        $getEpi = "SELECT `value` FROM ";
                        $getEpi .= $resource->getTableName(catalog_product_entity_text);
                        $getEpi .= " WHERE attribute_id = 172 AND store_id = $storeId AND entity_id = $entityId;";

                        $resultEpi = $readConnection->fetchOne($getEpi);

                        if (!empty($resultEpi)) {
                            if ($resultEpi != $epi) {
                                // O EPI do produto informado pelo CSV é diferente da descrição cadastrada no sitema
                                $updateEpi = "UPDATE ";
                                $updateEpi .= $resource->getTableName(catalog_product_entity_text);
                                $updateEpi .= " SET `value` = '$epi'";
                                $updateEpi .= " WHERE attribute_id = 172 AND store_id = $storeId AND entity_id = $entityId;";

                                $writeConnection->query($updateEpi);
                            }
                        } else {
                            // Não existe um EPI definido então vamos adicionar uma
                            $setEpi = "INSERT INTO ";
                            $setEpi .= $resource->getTableName(catalog_product_entity_text);
                            $setEpi .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setEpi .= " VALUE(4, 172, $storeId, $entityId, '$epi');";

                            $writeConnection->query($setEpi);
                        }
                    }

                    if ($brand) {
                        $getAttrCode = "SELECT `attribute_id` FROM ";
                        $getAttrCode .= $resource->getTableName(eav_attribute);
                        $getAttrCode .= " WHERE attribute_code = 'brand';";
                        $resultAttrCode = $readConnection->fetchOne($getAttrCode);

                        $getBrand = "SELECT eao.option_id FROM ";
                        $getBrand .= $resource->getTableName(eav_attribute_option_value) . " as eaov";
                        $getBrand .= " INNER JOIN ";
                        $getBrand .= $resource->getTableName(eav_attribute_option) . " as eao";
                        $getBrand .= " ON eao.option_id = eaov.option_id";
                        $getBrand .= " WHERE value = '$brand' and eao.attribute_id = $resultAttrCode;";

                        $optionId = $readConnection->fetchOne($getBrand);

                        if (empty($optionId)) {
                            // Adiciona a opção de marca
                            $setBrandOption = "INSERT ignore INTO ";
                            $setBrandOption .= $resource->getTableName(eav_attribute_option);
                            $setBrandOption .= " (attribute_id, sort_order)";
                            $setBrandOption .= " VALUES($resultAttrCode, 0);";

                            $writeConnection->query($setBrandOption);

                            // Pega o option_id para adicionar o value
                            $getBrandOption = "SELECT option_id FROM ";
                            $getBrandOption .= $resource->getTableName(eav_attribute_option);
                            $getBrandOption .= " ORDER BY option_id DESC LIMIT 1;";

                            $brandIdOption = $readConnection->fetchOne($getBrandOption);

                            // Adiciona a marca
                            $setBrandValue = "INSERT ignore INTO ";
                            $setBrandValue .= $resource->getTableName(eav_attribute_option_value);
                            $setBrandValue .= " (option_id, store_id, `value`)";
                            $setBrandValue .= " VALUES($brandIdOption, $storeId, '$brand');";

                            $writeConnection->query($setBrandValue);

                            // Pega o ID da marca adicionado
                            $getBrand = "SELECT option_id FROM ";
                            $getBrand .= $resource->getTableName(eav_attribute_option_value);
                            $getBrand .= " WHERE value = '$brand';";

                            $optionId = $readConnection->fetchOne($getBrand);
                        }

                        // Verifica se já existe para o produto uma marca cadastrada ou se precisa atualizar
                        $getProductBrand = "SELECT `value` FROM ";
                        $getProductBrand .= $resource->getTableName(catalog_product_entity_int);
                        $getProductBrand .= " WHERE attribute_id = $resultAttrCode AND entity_id = $entityId;";

                        $resultBrand = $readConnection->fetchOne($getProductBrand);

                        if (!empty($resultBrand)) {
                            if ($resultBrand != $optionId) {
                                // Marca informada pelo CSV é diferente da cadastrada então atualiza
                                $updateProductBrand = "UPDATE ";
                                $updateProductBrand .= $resource->getTableName(catalog_product_entity_int);
                                $updateProductBrand .= " SET `value` = $optionId";
                                $updateProductBrand .= " WHERE attribute_id = $resultAttrCode AND store_id = $storeId AND entity_id = $entityId;";

                                $writeConnection->query($updateProductBrand);
                            }
                        } else {
                            // não existe marca cadastrada então adiciona
                            // Associa a marca correspondente ao produto
                            $setBrandProduct = "INSERT ignore INTO ";
                            $setBrandProduct .= $resource->getTableName(catalog_product_entity_int);
                            $setBrandProduct .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setBrandProduct .= " VALUES(4, $resultAttrCode, $storeId, $entityId, $optionId);";

                            $writeConnection->query($setBrandProduct);
                        }
                    }

                    // Adiciona os produtos na categoria na store da distribuidora
                    if (!$magmiCategoriesStructure) {
                        $arrayCategories = explode(',', $categories);
                    } else {
                        $arrayCategories = getCategoriesIds($categories);
                    }
                    $iCategories = count($arrayCategories);
                    $ln = 1;
                    while ($ln <= $iCategories) {

                        $tmpCategory = $arrayCategories[$ln - 1];

                        $setCategory = "INSERT IGNORE INTO ";
                        $setCategory .= $resource->getTableName(catalog_category_product);
                        $setCategory .= " (category_id, product_id, position)";
                        $setCategory .= " VALUES($tmpCategory, $entityId, 0);";

                        $writeConnection->query($setCategory);

                        $ln++;
                    }

                    // Verifica se já existe um idErp do produto definido para o produto e cadastra ou atualiza
                    $iStoreView = count($storeView);
                    $ln = 1;

                    if ($idErp) {

                        while ($ln <= $iStoreView) {
                            $tmpStoreView = $storeView[$ln - 1];

                            $getIdErp = "SELECT `value` FROM ";
                            $getIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                            $getIdErp .= " WHERE attribute_id = 185 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                            $resultIdErp = $readConnection->fetchOne($getIdErp);

                            // adicionando o atributo ID_ERP para a store_id 0
                            $setIdErp = "INSERT ignore INTO ";
                            $setIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                            $setIdErp .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setIdErp .= " VALUE(4, 185, 0, $entityId, '');";

                            $writeConnection->query($setIdErp);
                            
                            if ((!empty($resultIdErp)) || ($resultIdErp == '')) {
                                    
                                // O id ERP informado pelo CSV é diferente da descrição cadastrada no sitema
                                $updateIdErp = "UPDATE ";
                                $updateIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                                $updateIdErp .= " SET `value` = '$idErp'";
                                $updateIdErp .= " WHERE attribute_id = 185 AND store_id = $tmpStoreView AND entity_id = $entityId;";
                                
                                $writeConnection->query($updateIdErp);
                            } else {

                                // Não existe um idErp para o produto definido então vamos adicionar um
                                $setIdErp = "INSERT INTO ";
                                $setIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                                $setIdErp .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                                $setIdErp .= " VALUE(4, 185, $tmpStoreView, $entityId, '$idErp');";

                                $writeConnection->query($setIdErp);
                            }
                            $ln++;
                        }
                    }


                    /**
                     *
                     * Tratando situações específicas para stores
                     *
                     */
                    // Executa as querys para cada storeView definida no array do arquivo configIntegra.php
                    $iStoreView = count($storeView);
                    $ln = 1;

                    while ($ln <= $iStoreView) {
                        $tmpStoreView = $storeView[$ln - 1];

                        // Verifica se já existe um idErp definido para o produto e cria ou atualiza
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
                                $updateIdErp .= " WHERE attribute_id = 185 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                                $writeConnection->query($updateIdErp);
                            }
                        } else {
                            if ($idErp) {
                                // Não existe um idErp para o produto definido então vamos adicionar um
                                $setIdErp = "INSERT INTO ";
                                $setIdErp .= $resource->getTableName(catalog_product_entity_varchar);
                                $setIdErp .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                                $setIdErp .= " VALUE(4, 185, $tmpStoreView, $entityId, '$idErp');";

                                $writeConnection->query($setIdErp);
                            }
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
                                $updateProductVisibility .= " WHERE attribute_id = 102 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                                $writeConnection->query($updateProductVisibility);
                            }
                        } else {
                            // Não existe visibilidade definida então vamos adicionar uma
                            $setVisibility = "INSERT INTO ";
                            $setVisibility .= $resource->getTableName(catalog_product_entity_int);
                            $setVisibility .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setVisibility .= " VALUE(4, 102, $tmpStoreView, $entityId, $visibility);";

                            $writeConnection->query($setVisibility);
                        }

                        // STATUS
                        // Verifica se já existe um status definida para o produto e cadastra ou atualiza
                        $getStatus = "SELECT `value` FROM ";
                        $getStatus .= $resource->getTableName(catalog_product_entity_int);
                        $getStatus .= " WHERE attribute_id = 96 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                        $resultStatus = $readConnection->fetchOne($getStatus);

                        if (!empty($resultStatus)) {
                            if ($resultStatus != $status && trim($status) != '') {
                                // A status informado pelo CSV é diferente da visibilidade cadastrada no sitema
                                $updateProductStatus = "UPDATE ";
                                $updateProductStatus .= $resource->getTableName(catalog_product_entity_int);
                                $updateProductStatus .= " SET `value` = $status";
                                $updateProductStatus .= " WHERE attribute_id = 96 AND store_id = $tmpStoreView AND entity_id = $entityId;";

                                $writeConnection->query($updateProductStatus);
                            }
                        } else if (trim($status) != '') {
                            // Não existe status definido então vamos adicionar um
                            $setStatus = "INSERT INTO ";
                            $setStatus .= $resource->getTableName(catalog_product_entity_int);
                            $setStatus .= " (entity_type_id, attribute_id, store_id, entity_id, `value`)";
                            $setStatus .= " VALUE(4, 96, $tmpStoreView, $entityId, $status);";

                            $writeConnection->query($setStatus);
                        }
                        // FIM STATUS
                        // Finaliza o enlace
                        $ln++;
                    }

                    // Array com os produtos adicionados pela integração
                    $produtosAdd[$sku] = array(
                        'name' => $name,
                        'url' => $url,
                        'urlPath' => $urlPath,
                        'productType' => $productType,
                        'manufacture' => $manufacture,
                        'status' => $status,
                        'visibilidade' => $visibility,
                        'taxClass' => $taxClass,
                        'description' => $description,
                        'shotDescription' => $shortDescription,
                        'whereToUse' => $whereToUse,
                        'packing' => $packing,
                        'animalSize' => $animalSize,
                        'animaAge' => $animalAge,
                        'animal' => $animal,
                        'administer' => $administer,
                        'idErp' => $idErp,
                        'categories' => $arrayCategories,
                    );
                } else {
                    // Array de produtos que não foram adicionado por algum motivo
                    $produtosNotAdd = array($sku);
                }
                echo "Linha => $i \n";
                $i++;
            }
        }
    }
}

function clean($string) {
    $string = str_replace('(', ' ', $string);
    $string = str_replace(' ', '_', $string);
    $string = str_replace(')', ' ', $string);
    $string = strtolower($string);

    return preg_replace('/[^A-Za-z0-9\_]/', '', $string); // Removes special chars.
}

function getCategoriesIds($categoriesOnMagmiFormat) {
    $categories = explode(';;', $categoriesOnMagmiFormat);

    $arrayCategoriesNames = array();

    foreach ($categories as $part) {
        $catParts = explode('/', $part);

		$level = 0;
        foreach ($catParts as $category) {

            $rootCat = get_string_between($category, '[', ']');

            if ($rootCat) {
                $arrayCategoriesNames[] = array('level' => 0, 'name' => $rootCat);
                $category = delete_all_between('[', ']', $category);
            }
			$arrayCategoriesNames[] = array('level' => $level++, 'name' => $category);
        }
    }

    $arrayCategoriesIds = array();

	$lastCategoryId = null;
	$rootCategoryId = null;
	foreach ($arrayCategoriesNames as $categoryName)
	{
		if ($categoryName['level'] == 0)
		{
			$lastCategoryId = $rootCategoryId;
		}

		if ($lastCategoryId)
		{
			$catId = getCategoryByName($categoryName['name'], $lastCategoryId);
		}
		else
		{
			$rootCategoryId = $catId = getCategoryByName($categoryName['name']);
		}
		if ($catId)
		{
			$arrayCategoriesIds[] = $catId;
			$lastCategoryId = $catId;
		}
		else
		{
			echo "Categoria: {$categoryName['name']} não encontrada.\n";
		}
	}

    return $arrayCategoriesIds;
}

function getCategoryByName($categoryName, $parentId = null)
{
	if ($parentId)
	{
		return Mage::getResourceModel('catalog/category_collection')
			->addFieldToFilter('name', $categoryName)
			->addFieldToFilter('parent_id', $parentId)
			->getFirstItem()
			->getId();
	} else
	{
		return Mage::getResourceModel('catalog/category_collection')
			->addFieldToFilter('name', $categoryName)
			->addFieldToFilter('parent_id', 1)
			->getFirstItem()
			->getId();
	}
}

function get_string_between($string, $start, $end) {
    $string = " " . $string;
    $ini = strpos($string, $start);
    if ($ini == 0)
        return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function delete_all_between($start, $end, $string) {
    $beginningPos = strpos($string, $start);
    $endPos = strpos($string, $end);
    if ($beginningPos === false || $endPos === false) {
        return $string;
    }

    $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

    return str_replace($textToDelete, '', $string);
}
