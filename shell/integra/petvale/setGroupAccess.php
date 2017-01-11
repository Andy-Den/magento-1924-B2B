<?php

require_once './configIntegra.php';

$lines = file("$directoryImp/customers/setGroupAccess.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
    foreach ($lines as $key => $value) {
        $i++;
        $temp = str_getcsv($value, '|', "'");

        $idErp = $temp[0];
        $idGroup = $temp[1];
        $idRep = $temp[2];


        // pega o entity_id do cliente
        $getEntityId = "SELECT ce.entity_id FROM customer_entity_varchar AS cev 
                    INNER JOIN customer_entity AS ce
                    ON cev.entity_id = ce.entity_id AND cev.attribute_id = 183
                    WHERE cev.`value` = '$idErp' AND ce.website_id = $websiteId;";
        
        $entityId = $readConnection->fetchOne($getEntityId);

        if ($idRep):
            // Consulta o id do representante informado
            $getEntityRepId = "SELECT id FROM fvets_salesrep where id_erp = $idRep and store_id in ($storeViewReps);";

            $entityRepId = $readConnection->fetchOne($getEntityRepId);
        endif;

        // Verifica se o cliente existe
        if ($entityId):

            //Consulta o grupo de acesso do cliente
            $getGroupAccess = "SELECT `value` FROM customer_entity_text WHERE entity_id = $entityId AND attribute_id = 191;";

            $groupAccessAtual = $readConnection->fetchOne($getGroupAccess);

            //Consulta o representante do cliente
            $getRep = "SELECT `value` FROM customer_entity_varchar WHERE `value` = $entityRepId AND attribute_id = 148;";

            $repAtual = $readConnection->fetchOne($getGroupAccess);


            // Valida se o GA atual é o mesmo que o informado no arquivo
            if ((empty($groupAccessAtual)) || (is_null($groupAccessAtual))):

                // caso o cliente não tenha um GA adiciona o GA informado
                $addGroupAccess = "INSERT INTO customer_entity_text (entity_type_id, attribute_id, entity_id, `value`) 
                VALUES (1,191,$entityId,'$idGroup');";

                $writeConnection->query($addGroupAccess);

            else:

                // caso já exista atualiza
                $updateGroupAccess = "UPDATE customer_entity_text set `value` = $idGroup WHERE entity_id = $entityId AND attribute_id = 191;";

                $writeConnection->query($updateGroupAccess);

            endif;

            if ($idRep):
                // Valida o Representante do cliente
                if ((empty($repAtual)) || (is_null($repAtual))):

                    // caso o cliente não tenha um GA adiciona o GA informado
                    $addRep = "INSERT INTO customer_entity_varchar (entity_type_id, attribute_id, entity_id, `value`) 
                VALUES (1,148,$entityId,'$entityRepId');";

                    $writeConnection->query($addRep);

                else:

                    // caso já exista atualiza
                    $updateRep = "UPDATE customer_entity_varchar set `value` = $entityRepId WHERE entity_id = $entityId AND attribute_id = 148;";

                    $writeConnection->query($updateRep);

                    echo "+";

                endif;
            endif;

        else:
            echo "\n$idErp - cliente não localizado\n";
        endif;
    }

    echo "\n";
}