<?php
require_once './configIntegra.php';

// Rotina para definir Grupo de acesso e representante comercial para customers

$lines = file("$directoryImp/customers/complementCustomers.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
    $clienteRep = "ID ERP|Rep Antigo|Rep2 Novo|Obs\n";
    foreach ($lines as $key => $value) {
        $i++;
        $temp = str_getcsv($value, '|', "'");

        $idErp = $temp[0];
        $rep1 = $temp[1];
        $rep2 = $temp[2];

        /**
         * Verifica se o representante está cadastrado no IntegraVet
         */
        if ($rep1 != NULL){
            $getIdRep1 = "SELECT id FROM ";
            $getIdRep1 .= $resource->getTableName(fvets_salesrep);
            $getIdRep1 .= " WHERE store_id IN ($storeViewAll) AND ";
            $getIdRep1 .= " id_erp = '$rep1';";

            $idRep1 = $readConnection->fetchOne($getIdRep1);
        } else {
            $idRep1 = NULL;
            $clienteRep .= "$idErp|$idRep1||representante não localizado no IntegraVet\n";
        }

        if ($rep2 != NULL){
            $getIdRep2 = "SELECT id FROM ";
            $getIdRep2 .= $resource->getTableName(fvets_salesrep);
            $getIdRep2 .= " WHERE store_id IN ($storeViewAll) AND ";
            $getIdRep2 .= " id_erp = '$rep2';";

            $idRep2 = $readConnection->fetchOne($getIdRep2);
        } else {
            $idRep2 = NULL;
            $clienteRep .= "$idErp|$idRep1||representante não localizado no IntegraVet\n";
        }


        /**
         * Procura pelo id_erp do cliente cliente para atualização das informações
         * Caso não encontre o id_erp pesquisa pelo email sempre validando o website_id
         */
        $getEntityId = "SELECT ce.entity_id FROM ";
        $getEntityId .= $resource->getTableName(customer_entity_varchar) . " as cev";
        $getEntityId .= " INNER JOIN " . $resource->getTableName(customer_entity) . " as ce ";
        $getEntityId .= "ON cev.entity_id = ce.entity_id ";
        $getEntityId .= "WHERE cev.attribute_id = 183 AND cev.`value` = $idErp AND ce.website_id = $websiteId;";

        $entityId = $readConnection->fetchOne($getEntityId);

        if ($entityId != NULL) {
            // definindo o primeiro representante caso informado no arquivo csv
            if (($rep1 != NULL) && $rep2 == NULL){
                $getCustomerSalesRep = "SELECT `value` FROM ";
                $getCustomerSalesRep .= $resource->getTableName(customer_entity_varchar);
                $getCustomerSalesRep .= " WHERE entity_id = $entityId AND attribute_id = 148;";

                $idCustomerSalesRep = $readConnection->fetchOne($getCustomerSalesRep);

                if ($idCustomerSalesRep != NULL) {
                    if ($idCustomerSalesRep != $idRep1) {
                        $updateCustomerSalesRep = "UPDATE ";
                        $updateCustomerSalesRep .= $resource->getTableName(customer_entity_varchar);
                        $updateCustomerSalesRep .= " SET `value` = '$idRep1' WHERE entity_id = $entityId";
                        $updateCustomerSalesRep .= " AND attribute_id = 148 AND `value` = $idCustomerSalesRep;";

                        $writeConnection->query($updateCustomerSalesRep);

                        $clienteRep .= "$idErp|$idCustomerSalesRep|$idRep1|atualizado\n";
                    }
                }
                if(($idCustomerSalesRep == FALSE) && ($idRep1 != NULL)) {
                    $setCustomerSalesRep = "INSERT INTO ";
                    $setCustomerSalesRep .= $resource->getTableName(customer_entity_varchar);
                    $setCustomerSalesRep .= " (entity_type_id, attribute_id, entity_id, `value`)";
                    $setCustomerSalesRep .= " VALUES(1, 148, $entityId, '$idRep1');";

                    echo  "\n\n\n $setCustomerSalesRep \n\n\n";

                    $writeConnection->query($setCustomerSalesRep);

                    $clienteRep .= "$idErp||$idRep1|adicionado\n";
                }

            }
            if (($rep2 != NULL) && $rep1 == NULL){
                $getCustomerSalesRep = "SELECT `value` FROM ";
                $getCustomerSalesRep .= $resource->getTableName(customer_entity_varchar);
                $getCustomerSalesRep .= " WHERE entity_id = $entityId AND attribute_id = 148;";

                $idCustomerSalesRep = $readConnection->fetchOne($getCustomerSalesRep);

                if ($idCustomerSalesRep != NULL) {
                    if ($idCustomerSalesRep != $idRep1) {
                        $updateCustomerSalesRep = "UPDATE ";
                        $updateCustomerSalesRep .= $resource->getTableName(customer_entity_varchar);
                        $updateCustomerSalesRep .= " SET `value` = '$idRep2' WHERE entity_id = $entityId";
                        $updateCustomerSalesRep .= " AND attribute_id = 148 AND `value` = '$idCustomerSalesRep';";

                        $writeConnection->query($updateCustomerSalesRep);

                        $clienteRep .= "$idErp|$idCustomerSalesRep|$idRep1|atualizado\n";
                    }
                }
                if(($idCustomerSalesRep == FALSE) && ($idRep1 != NULL)) {
                    $setCustomerSalesRep = "INSERT INTO ";
                    $setCustomerSalesRep .= $resource->getTableName(customer_entity_varchar);
                    $setCustomerSalesRep .= " (entity_type_id, attribute_id, entity_id, `value`)";
                    $setCustomerSalesRep .= " VALUES(1, 148, $entityId, '$idRep2');";

                    $writeConnection->query($setCustomerSalesRep);

                    $clienteRep .= "$idErp||$idRep1|adicionado\n";
                }
            }

            if (($rep1 != NULL) && ($rep2 != NULL)){
                $getCustomerSalesRep = "SELECT `value` FROM ";
                $getCustomerSalesRep .= $resource->getTableName(customer_entity_varchar);
                $getCustomerSalesRep .= " WHERE entity_id = $entityId AND attribute_id = 148;";

                $idCustomerSalesRep = $readConnection->fetchOne($getCustomerSalesRep);

                $arrayReps = explode(',', $idCustomerSalesRep);

                if ($idCustomerSalesRep != NULL) {

                    $srcRep1 = in_array($idRep1,$arrayReps);
                    $srcRep2 = in_array($idRep2,$arrayReps);


                    if (($srcRep1 == FALSE) || ($srcRep2 == FALSE)) {
                        $updateCustomerSalesRep = "UPDATE ";
                        $updateCustomerSalesRep .= $resource->getTableName(customer_entity_varchar);
                        $updateCustomerSalesRep .= " SET `value` = '$idRep1,$idRep2' WHERE entity_id = $entityId";
                        $updateCustomerSalesRep .= " AND attribute_id = 148 AND `value` = $idCustomerSalesRep;";

                        $writeConnection->query($updateCustomerSalesRep);

                        $clienteRep .= "$idErp|$idCustomerSalesRep|$idRep1,$idRep2|atualizados\n";
                    }
                }
                if(($idCustomerSalesRep == FALSE) && (($idRep1 || $idRep2) != NULL)) {
                    $setCustomerSalesRep = "INSERT INTO ";
                    $setCustomerSalesRep .= $resource->getTableName(customer_entity_varchar);
                    $setCustomerSalesRep .= " (entity_type_id, attribute_id, entity_id, `value`)";
                    $setCustomerSalesRep .= " VALUES(1, 148, $entityId, '$idRep1,$idRep2');";

                    $writeConnection->query($setCustomerSalesRep);

                    $clienteRep .= "$idErp||$idRep1,$idRep2|adicionados\n";
                }
            }
        } else {
            $clienteRep .= "$idErp|||cliente não encontrado no IntegraVet\n";
        }

        echo "Linha >>>> $i \n";
    }
}

mkdir("$directoryRep/customers/salesRep/$currentDate", 0777, true);
// Gera o arquivo de clientes com grupos de acessos atualziados

print_r($clienteRep);

if (!is_null($clienteRep)){
    file_put_contents("$directoryRep/customers/salesRep/$currentDate/salesCustomers.csv", $clienteRep);
    echo "\n Arquivo de relação de clientes que sofreram alteraçoes de representante comercial.\n";
}