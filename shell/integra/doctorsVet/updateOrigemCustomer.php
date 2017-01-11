<?php

require_once './configIntegra.php';

/**
 * Em função da dependência de clientes com suas respectivas tabelas de preços roda-se primeiro
 * a integração de tabela de preços
 */
//require_once './importTablePrice.php';

/**
 * Inicia a integração dos customers
 */
$totalOrigemAtualizado = 0;
$totalIdErpNaoLocalizado = 0;
$totalSemData = 0;


$lines = file("$directoryImp/customers/customers.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
    echo "\n\n" . "arquivo csv vazio ou não existe" . "<br/>\n";
} else {
    foreach ($lines as $key => $value) {
        $i ++;
        $temp = str_getcsv($value, '|', "'");

        /* variáveis utilizadas na integração */
        $tipo = $temp[0];
        $idErp = trim($temp[1]);
        if ((!empty($temp[20]))): $lastOrderErp = date('Y-m-d', strtotime($temp[20]));
        else: $lastOrderErp = NULL;
        endif;

        if (is_null($lastOrderErp)) {
            echo "\n\n O cliente com o ID ERP: $idErp Não possue data da ultima compra definida <br/>\n";
            $semData .= "$idErp não possui data da ultima compra enviado pelo ERP;\n";
            $totalSemData ++;
        }

        if ($tipo != 3) {
            // atualiza a origem do cliente
            $entityId = NULL;
            $getEntityId = "SELECT ce.entity_id FROM ";
            $getEntityId .= $resource->getTableName(customer_entity_varchar) . " as cev";
            $getEntityId .= " INNER JOIN " . $resource->getTableName(customer_entity) . " as ce ";
            $getEntityId .= "ON cev.entity_id = ce.entity_id ";
            $getEntityId .= "WHERE cev.attribute_id = 183 AND cev.`value` = $idErp AND ce.website_id = $websiteId;";

            $entityId = $readConnection->fetchOne($getEntityId);

            if (!empty($entityId)) {
                // Verifica se já existe uma data de ultima compra definida pelo ERP na primeira carga
                $getLastOrderErpAtual = "SELECT `value` FROM ";
                $getLastOrderErpAtual .= $resource->getTableName(customer_entity_varchar);
                $getLastOrderErpAtual .= " WHERE attribute_id = 197 AND entity_id = $entityId;";

                $lastOrderErpAtual = $readConnection->fetchOne($getLastOrderErpAtual);

                // Caso não exista uma data cadastrada, efetiva o cadastro se já existe não faz nada
                if (empty($lastOrderErpAtual) && $lastOrderErpAtual != '') {
                    $setLastOrderErp = "INSERT INTO ";
                    $setLastOrderErp .= $resource->getTableName(customer_entity_varchar);
                    $setLastOrderErp .= " (entity_type_id, attribute_id, entity_id, `value`)";
                    $setLastOrderErp .= " VALUES(1, 197, $entityId, '$lastOrderErp');";
                    
                    $writeConnection->query($setLastOrderErp);
                    
                } elseif (($lastOrderErpAtual != $lastOrderErp) || $lastOrderErpAtual == NULL) {
                    $upLastOrderErp = "UPDATE ";
                    $upLastOrderErp .= $resource->getTableName(customer_entity_varchar);
                    $upLastOrderErp .= " SET `value` = '$lastOrderErp'";
                    $upLastOrderErp .= " WHERE attribute_id = 197 AND entity_id = $entityId;";

                    $writeConnection->query($upLastOrderErp);

                    $lastOrderErpAtual = $lastOrderErp;
                }

                // Pega da data da ultima compra do cliente no IntegraVet
                // Comparo as datas $lastOrderErpAtual com a $lastOrderIntegra em função da margem de 90 dias da data atual
                // Caso a data $lastOrderErpAtual for maior que 90 dias e a data da $lastOrderIntegra é menor que 90 dias
                // O cliente se torna um cliente do Site, caso contrário ele mantém o cliente como ERP

                $getLastOrderIntegra = "SELECT created_at FROM ";
                $getLastOrderIntegra .= $resource->getTableName(sales_flat_order);
                $getLastOrderIntegra .= " WHERE entity_id = $entityId AND store_id = $storeId;";

                $lastOrderIntegra = $readConnection->fetchOne($getLastOrderIntegra);

                // Define se é cliente do tipo 1% ou 2% em função da data da ultima compra realizada
                if ($lastOrderIntegra == true):$lastOrderIntegra = date('Y-m-d', strtotime($lastOrderIntegra));
                else:$lastOrderIntegra;
                endif;
                $lastOrderErpAtual = date('Y-m-d', strtotime($lastOrderErpAtual));
                $maxDate = date('Y-m-d', strtotime($currentDate . ' - 90 day'));

                if (($lastOrderIntegra == true) && ($lastOrderErpAtual <= $maxDate)) {
                    $origem = 'SITE';
                } else {
                    $origem = 'ERP';
                }

                // Adiciona ou atualiza a origem do cadastro
                $getOrigem = "SELECT `value` FROM ";
                $getOrigem .= $resource->getTableName(customer_entity_varchar);
                $getOrigem .= " WHERE attribute_id = 186 AND entity_id = $entityId;";

                $origemAtual = $readConnection->fetchOne($getOrigem);

                if ($origemAtual != 'SITE') {
                    if ($origemAtual == false) {
                        $addOrigem = "INSERT INTO ";
                        $addOrigem .= $resource->getTableName(customer_entity_varchar);
                        $addOrigem .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addOrigem .= "VALUES(1, 186, $entityId, '$origem');";

                        $writeConnection->query($addOrigem);

                        echo "\n\n A origem do cliente com o ID ERP: $idErp foi definido como: $origem<br/>\n";
                        $origemDefinido .= "$idErp foi definido com a origem: $origem;\n";
                    }
                    if ($origemAtual != $origem) {
                        $updateOrigemAtual = "UPDATE ";
                        $updateOrigemAtual .= $resource->getTableName(customer_entity_varchar);
                        $updateOrigemAtual .= " SET `value` = '$origem'";
                        $updateOrigemAtual .= " WHERE entity_id = $entityId AND attribute_id = 186;";

                        $writeConnection->query($updateOrigemAtual);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);

                        echo "\n\n A origem do cliente com o ID ERP: $idErp foi atualizado para: $origem<br/>\n";
                        $origemDefinido .= "ID ERP: $idErp foi atualizado para origem: $origem;\n";
                        $totalOrigemAtualizado++;
                    }
                }
            } else {
                echo "\n\n Cliente não localizado pelo ID ERP: $idErp<br/>\n";
                $idErpNaoLocalizado .= "ID ERP: $idErp não foi localizado em nossa base de dados;\n";
                $totalIdErpNaoLocalizado++;
            }

            // finaliza atualização da origem do cliente
            $entityId = NULL;
            echo "\n\n Linha >>>>> $i <br/>\n";
        }
    }

    mkdir("$directoryRep/$currentDate", 0777, true);

    // Gera o arquivo com clientes com a origem definida
    if (!is_null($origemDefinido)) {
        file_put_contents("$directoryRep/$currentDate/clienteOrigem.csv", $origemDefinido);
        echo "\n Arquivo com Relação de clientes e a sua origem foi criado.<br/>\n";
    }

    // Gera o arquivo com clientes não localizados em nossa base de dados
    if (!is_null($idErpNaoLocalizado)) {
        file_put_contents("$directoryRep/$currentDate/clienteNaoLocalizado.csv", $idErpNaoLocalizado);
        echo "\n Arquivo com Relação de clientes não localizados em nossa base de dados foi criado.<br/>\n";
    }
    // Gera o arquivo com clientes não localizados em nossa base de dados
    if (!is_null($semData)) {
        file_put_contents("$directoryRep/$currentDate/clienteSemData.csv", $semData);
        echo "\n Arquivo com Relação de clientes data da ultima compra foi criado.<br/>\n";
    }
}