<?php
require_once './configIntegra.php';

// Rotina para definir Grupo de acesso e representante comercial para customers

$lines = file("$directoryImp/customers/complementCustomers.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
    $clienteGA = "ID ERP|Purina|MSD|Obs\n";
    foreach ($lines as $key => $value) {
        $i++;
        $temp = str_getcsv($value, '|', "'");

        $idErp = $temp[0];
        $gA1 = $temp[3];
        $gA2 = $temp[4];

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

        // Caso o cliente tenha sido localizado no IntegraVet pelo ID-Erp procedemos com a ãtualização do grupo de acesso
        if ($entityId != NULL){
            // Verifica se o idErp do cliente está associado à algum grupo de acesso
            if (($gA1 || $gA2) != NULL){
                // De acordo com o arquivo csv o cliente está associado a pelo menos 1 ou nos dois grupos de acessos
                // Caso esteja associado à um dos grupos cadastra apenas ao que tem acesso

                // Verifica se o entity_id do customer já possue um grupo de acesso e em seguida faz a atualização
                $getGA = "SELECT `value` FROM ";
                $getGA .= $resource->getTableName(customer_entity_text);
                $getGA .= " WHERE entity_id = $entityId AND attribute_id = 191;";

                $gA = $readConnection->fetchOne($getGA);

                if (($gA1 == 1) && ($gA2 == NULL)){
                    // Define o cliente no grupo de acesso Purina
                    if($gA != NULL) {
                        $updateGA = "UPDATE ";
                        $updateGA .= $resource->getTableName(customer_entity_text);
                        $updateGA .= " SET `value` = '$storeViewPurina' WHERE entity_id = $entityId AND attribute_id = 191; ";

                        $writeConnection->query($updateGA);

                    } else {
                        $setGA = "INSERT INTO ";
                        $setGA .= $resource->getTableName(customer_entity_text);
                        $setGA .= " (entity_type_id, attribute_id, entity_id, `value`)";
                        $setGA .= " VALUES(1,191,$entityId,'$storeViewPurina');";

                        $writeConnection->query($setGA);
                    }
                    $clienteGA .= "$idErp|1|\n";
                }
                if (($gA2 == 1) && ($gA1 == NULL)){
                    // Cadastra o cliente no grupo de acesso MSD
                    if($gA != NULL) {
                        $updateGA = "UPDATE ";
                        $updateGA .= $resource->getTableName(customer_entity_text);
                        $updateGA .= " SET `value` = '$storeViewMsd' WHERE entity_id = $entityId AND attribute_id = 191; ";

                        $writeConnection->query($updateGA);

                    } else {
                        $setGA = "INSERT INTO ";
                        $setGA .= $resource->getTableName(customer_entity_text);
                        $setGA .= " (entity_type_id, attribute_id, entity_id, `value`)";
                        $setGA .= " VALUES(1,191,$entityId,'$storeViewMsd');";

                        $writeConnection->query($setGA);
                    }
                    $clienteGA .= "$idErp||1\n";

                }
                if (($gA1 && $gA2) == 1){
                    // De acordo com o arquivo csv o cliente está associado a todos os grupos de acessos
                    if($gA != NULL) {
                        $updateGA = "UPDATE ";
                        $updateGA .= $resource->getTableName(customer_entity_text);
                        $updateGA .= " SET `value` = '$storeViewAll' WHERE entity_id = $entityId AND attribute_id = 191; ";

                        $writeConnection->query($updateGA);

                    } else {
                        $setGA = "INSERT INTO ";
                        $setGA .= $resource->getTableName(customer_entity_text);
                        $setGA .= " (entity_type_id, attribute_id, entity_id, `value`)";
                        $setGA .= " VALUES(1,191,$entityId,'$storeViewAll');";

                        $writeConnection->query($setGA);
                    }
                    $clienteGA .= "$idErp|1|1\n";
                }
            } else {
                // De acordo com o arquivo o cliente não está associado a nenhum grupo de acesso
                // Deve-se cadastrar o cliente em todos os grupos de acessos
                // Verifica se o entity_id do customer já possue um grupo de acesso e em seguida faz a atualização
                $gA = NULL;
                $getGA = "SELECT `value` FROM ";
                $getGA .= $resource->getTableName(customer_entity_text);
                $getGA .= " WHERE entity_id = $entityId AND attribute_id = 191;";

                $gA = $readConnection->fetchOne($getGA);

                // De acordo com o arquivo csv o cliente está associado a todos os grupos de acessos
                if($gA != NULL) {
                    $updateGA = "UPDATE ";
                    $updateGA .= $resource->getTableName(customer_entity_text);
                    $updateGA .= " SET `value` = '$storeViewAll' WHERE entity_id = $entityId AND attribute_id = 191; ";

                    $writeConnection->query($updateGA);

                } else {
                    $setGA = "INSERT INTO ";
                    $setGA .= $resource->getTableName(customer_entity_text);
                    $setGA .= " (entity_type_id, attribute_id, entity_id, `value`)";
                    $setGA .= " VALUES(1,191,$entityId,'$storeViewAll');";

                    $writeConnection->query($setGA);
                }
                $clienteGA .= "$idErp|1|1|Não definido GA no Arquivo csv.\n";

            }

        } else {
            // cliente não localizado no IntegraVet
            $clienteGA .= "$idErp|||Não encontrado no IntegraVet\n";
        }

        echo "Linha >>> $i\n";
    }
}

mkdir("$directoryRep/customers/groups/GA/$currentDate", 0777, true);
// Gera o arquivo de clientes com grupos de acessos atualziados
if (!is_null($clienteGA)){
    file_put_contents("$directoryRep/customers/groups/GA/$currentDate/reportGA.csv", $clienteGA);
    echo "\n Arquivo de relação de clientes com grupos de acesso atualizados foi criado.\n";
}