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
$totalStatusAtualizado = null;


$lines = file("$directoryImp/customers/customers.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
    echo "\n\n" . "arquivo csv vazio ou não existe" . "<br/>\n";
} else {
    foreach ($lines as $key => $value) {
        $i ++;
        $temp = str_getcsv($value, '|', "'");

        /* variáveis utilizadas na integração */
        $idErp = trim($temp[1]);
        $status = $temp[21];

        // atualiza a origem do cliente
        $entityId = NULL;
        $getCustomer = "SELECT ce.entity_id, is_active FROM ";
        $getCustomer .= $resource->getTableName(customer_entity_varchar) . " as cev";
        $getCustomer .= " INNER JOIN " . $resource->getTableName(customer_entity) . " as ce ";
        $getCustomer .= "ON cev.entity_id = ce.entity_id ";
        $getCustomer .= "WHERE cev.attribute_id = 183 AND cev.`value` = $idErp AND ce.website_id = $websiteId;";

        $customer = $readConnection->fetchAll($getCustomer);

        $entityId = $customer[0]['entity_id'];
        $statusIntegra = $customer[0]['is_active'];
        
        if (!empty($entityId) && ($statusIntegra != $status)) {

            // caso o id erp tenha sido encontrado atualiza o status
            
            if ($status == 0){
                $status = 1;
            }
            else{
                $status = 0;
            }
            
            $updateStatus = "UPDATE customer_entity SET is_active = $status WHERE entity_id = $entityId";
            
            $writeConnection->query($updateStatus);

            updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);

            echo "\n\n O status do cliente com o ID ERP: $idErp foi atualizado para: $status<br/>\n";
            $statusDefinido .= "ID ERP: $idErp foi atualizado para status: $status;\n";
            $totalStatusAtualizado++;
        } else {
            echo "\n\n Cliente não localizado pelo ID ERP: $idErp<br/>\n";
            $idErpNaoLocalizado .= "ID ERP: $idErp não foi localizado em nossa base de dados;\n";
            $totalIdErpNaoLocalizado++;
        }

        // finaliza atualização da origem do cliente
        echo "\n\n Linha >>>>> $i <br/>\n";
    }

    mkdir("$directoryRep/$currentDate", 0777, true);

    // Gera o arquivo com clientes com a origem definida
    if (!is_null($statusDefinido)) {
        file_put_contents("$directoryRep/$currentDate/clienteStatus.csv", $statusDefinido);
        echo "\n Arquivo com Relação de clientes e seu Status foi criado.<br/>\n";
    }

    // Gera o arquivo com clientes não localizados em nossa base de dados
    if (!is_null($idErpNaoLocalizado)) {
        file_put_contents("$directoryRep/$currentDate/clienteNaoLocalizado.csv", $idErpNaoLocalizado);
        echo "\n Arquivo com Relação de clientes não localizados em nossa base de dados foi criado.<br/>\n";
    }
}