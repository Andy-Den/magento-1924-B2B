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

$totalEmailRepetido = 0;
$totalClienteAdicionado = 0;
$totalClienteAtualizado = 0;


$lines = file("$directoryImp/customers/purina+msd.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
    foreach ($lines as $key => $value) {
        $i++;
        $temp = str_getcsv($value, '|', "'");

        $idErp = $temp[0];
        $idGroup1 = $temp[1];
        $idGroup2 = $temp[2];


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

        if (!empty($entityId)){
            if ($idGroup1 == $idGroup2) {
                // Pega o ID da tabela cadastrada no IntegraVet
                $getIdGroupIntegra = "SELECT customer_group_id FROM ";
                $getIdGroupIntegra .= $resource->getTableName(customer_group);
                $getIdGroupIntegra .= " WHERE id_tabela = '$idGroup1';";

                $idGroupIntegra = $readConnection->fetchOne($getIdGroupIntegra);

                // Pega o grupo que o cliente está cadastrado atualmente
                $getIdGroupAtual = "SELECT group_id FROM customer_entity ";
                $getIdGroupAtual .= $resource->getTableName(customer_entity);
                $getIdGroupAtual .= " WHERE entity_id = $entityId AND website_id = $websiteId;";

                $idGroupAtual = $readConnection->fetchOne($getIdGroupAtual);

                // compara se o grupo informado pelo ERP é diferente do grupo atualmente cadastrado
                if ($idGroupIntegra != $idGroupAtual){
                    $updateGroup = "UPDATE ";
                    $updateGroup .= $resource->getTableName(customer_entity);
                    $updateGroup .= " SET group_id = $idGroupIntegra";
                    $updateGroup .= " WHERE entity_id = $entityId AND website_id = $websiteId;";

                    $writeConnection->query($updateGroup);

                    $grupoAtualizado .= "O cliente com ID ERP: $idErp teve o grupo atualziado para $idGrup1.\n";
                }
            }

            if ($idGroup1 != $idGroup2) {
                echo "\n\n\n$idGroup1+$idGroup2\n\n\n";

                $getIdGroupIntegra = "SELECT customer_group_id FROM ";
                $getIdGroupIntegra .= $resource->getTableName(customer_group);
                $getIdGroupIntegra .= " WHERE id_tabela = '$idGroup1+$idGroup2';";

                $idGroupIntegra = $readConnection->fetchOne($getIdGroupIntegra);

                echo "\n\n\n $idGroupIntegra \n\n\n";

                if (!empty($idGroupIntegra)){
                    $updateGroup = "UPDATE ";
                    $updateGroup .= $resource->getTableName(customer_entity);
                    $updateGroup .= " SET group_id = $idGroupIntegra";
                    $updateGroup .= " WHERE entity_id = $entityId AND website_id = $websiteId;";

                    $writeConnection->query($updateGroup);

                    $grupoAtualizado .= "O cliente com ID ERP: $idErp teve o grupo atualziado para $idGrupo1.\n";

                } else {
                    $grupoInvalido .= "O Id ERP: $idErp está usando uma combinação de grupo inválida: $idGroup1+$idGroup2.\n";
                }
            }
        } else {
            echo "\n\n\ncliente não localizado\n\n\n";
            $clienteNaoLocalizado .= "O cliente com id erp: $idErp não foi localizado.\n";
        }
    }
}

mkdir("$directoryRep/customers/groups/$currentDate", 0777, true);

// Gera o arquivo de clientes com grupos atualziados
if (!is_null($grupoAtualizado)){
    file_put_contents("$directoryRep/customers/groups/$currentDate/clientesGrupoAtualizado.csv", $grupoAtualizado);
    echo "\n Arquivo de relação de clientes com grupos atualizados foi criado.\n";
}

// Gera o arquivo de clientes com combinação de grupos invalidos
if (!is_null($grupoInvalido)){
    file_put_contents("$directoryRep/customers/groups/$currentDate/clientesCombinacaoGrupoInvalido.csv", $grupoInvalido);
    echo "\n Arquivo de relação de clientes com grupos inválidos foi criado.\n";
}

// Gera o arquivo de clientes com combinação de grupos invalidos
if (!is_null($clienteNaoLocalizado)){
    file_put_contents("$directoryRep/customers/groups/$currentDate/clientesNaoLocalizado.csv", $clienteNaoLocalizado);
    echo "\n Arquivo de relação de clientes que não foram localizado foi criado.\n";
}