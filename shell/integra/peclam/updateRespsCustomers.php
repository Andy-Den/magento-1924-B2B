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

$lines = file("$testDirectoryImport/customers.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {

    $clientes_nao_cadastrados = NULL;

    foreach ($lines as $key => $value) {
        $i ++;
        $temp = str_getcsv($value, '|', "'");

        /* variáveis utilizadas na integração */
        $idErp = trim($temp[1]);
        $idRep = $temp[18];


        // Pega o entity_id do customer
        $getEntityId = "SELECT ce.entity_id FROM customer_entity_varchar AS cev
                        INNER JOIN customer_entity AS ce
                        ON cev.entity_id = ce.entity_id AND attribute_id = 183
                        WHERE ce.website_id = $websiteId AND cev.`value` = '$idErp';";

        $entityId = $readConnection->fetchOne($getEntityId);

        if ($entityId == TRUE) {
            // Define o Representante do cliente
            // Pega o ID do representante referente ao IntegraVet

            $getRepIntegra = "SELECT id FROM ";
            $getRepIntegra .= $resource->getTableName(fvets_salesrep);
            $getRepIntegra .= " WHERE id_erp = '$idRep' AND store_id = $stockId;";

            $idRepIntegra = $readConnection->fetchOne($getRepIntegra);
            
            if ($idRepIntegra == true) {
                $getIdRep = "SELECT `value` FROM ";
                $getIdRep .= $resource->getTableName(customer_entity_varchar);
                $getIdRep .= " WHERE attribute_id = 148 AND entity_id = $entityId;";

                $idRepAtual = $readConnection->fetchOne($getIdRep);
                
                if (($idRepAtual == false) && (!is_null($idRepAtual))) {
                    $addIdRep = "INSERT INTO ";
                    $addIdRep .= $resource->getTableName(customer_entity_varchar);
                    $addIdRep .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                    $addIdRep .= "VALUES(1, 148, $entityId, '$idRepIntegra');";

                    $writeConnection->query($addIdRep);
                } elseif ($idRepAtual != $idRep) {
                    $updateIdRepAtual = "UPDATE ";
                    $updateIdRepAtual .= $resource->getTableName(customer_entity_varchar);
                    $updateIdRepAtual .= " SET `value` = '$idRepIntegra'";
                    $updateIdRepAtual .= " WHERE entity_id = $entityId AND attribute_id = 148;";
                    
                    $writeConnection->query($updateIdRepAtual);
                }
            }
        }
        echo "\n\n Linha >>>>> $i \n\n";
    }

}