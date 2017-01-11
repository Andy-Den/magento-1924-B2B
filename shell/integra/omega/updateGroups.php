<?php
require_once './configIntegra.php';

/**
 * Em função da dependência de clientes com suas respectivas tabelas de preços roda-se primeiro
 * a integração de tabela de preços
 */

// Primeiro pegamos todos os Ids dos Grupos enviados pelo ERP
// Montamos um arquivo dentro do diretório padrão de grupos e
// Montamos um array com os Ids dos grupos para fazer a comparação e na sequência a atualização
// Dos grupos que devem ser mantidos e ou criados
$lines = file("$directoryImp/table_prices/table_prices.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {

    foreach ($lines as $key => $value) {
        $i ++;
        $temp = str_getcsv($value,'|',"'");
        $idGroupErp = $temp[0];
        $nameGroupErp = $temp[1];
        $idErpProduct = $temp[2];
        $price = $temp[3];

        var_dump($idGroupTmp);
        
        if($idGroupTmp != $idGroupErp){
            $outIdGpr .= "$idGroupErp|$nameGroupErp\n";
            $itemArrayGprId .= "$idGroupErp|$nameGroupErp,";
        }

        $idGroupTmp = $idGroupErp;
        $idGroup == NULL;
    }
}

// criando o arquivo com os grupos enviados pelo ERP no diretório correspondente aos grupos
file_put_contents("$directoryImp/groups/groups.csv", $outIdGpr);

// criando o array com os grupos enviados pelo ERP
$arrayGprIdErp = explode(",",$itemArrayGprId);

// pega o total de registro de grupos da distribuidora
$getIdGroupsAtual = "SELECT customer_group_id, customer_group_code, id_tabela FROM ";
$getIdGroupsAtual .= $resource->getTableName(customer_group);
$getIdGroupsAtual .= " WHERE website_id = $websiteId";

$idsGroupsAtual = $readConnection->fetchAll($getIdGroupsAtual);

$lnGrp = count($idsGroupsAtual);

$lnGrp --;
$iLnGrp = 0;
$itemArrayGprId = null;
while($iLnGrp <= $lnGrp){
    $id = $idsGroupsAtual[$iLnGrp]['customer_group_id'];
    $nameGroupAtual = $idsGroupsAtual[$iLnGrp]['customer_group_code'];
    $idGroupAtual = $idsGroupsAtual[$iLnGrp]['id_tabela'];

    // Montando o array dos Grupos cadastrados no banco de dados
    $itemArrayGprId .= "$idGroupAtual|$nameGroupAtual,";

    $iLnGrp ++;
}

// criando o array com os grupos cadastrados no integravets
$arrayGprIdAtual = explode(",",$itemArrayGprId);

print_r($arrayGprIdAtual);
print_r($arrayGprIdErp);

echo "================================== \n\n";
// Checa os grupos que o ERP está mandando e o IntegraVets já possui cadastrado
$intersect = array_intersect($arrayGprIdAtual, $arrayGprIdErp);

// Checa os grupos que o ERP não está mais mandando e o IntegraVets ainda possui cadastrado
// E deverão ser deletados no final do procedimento
$diffAtualErp = array_diff($arrayGprIdAtual, $arrayGprIdErp);

// Checa os grupos que o ERP está mandando e o IntegraVets ainda não possui cadastrado
// E deverão ser adicionados
$diffErpAtual = array_diff($arrayGprIdErp, $arrayGprIdAtual);

echo "Grupos em comum entre o IntegraVet e o ERP \n\n";
print_r($intersect);

echo "Grupos que estão cadastrados mas o ERP não está mais enviando e deverão ser removidos\n\n";
print_r($diffAtualErp);

echo "Grupos que o ERP está enviando e ainda não consta no IntegraVet e deverão ser criados\n\n";
print_r($diffErpAtual);

// Caso haja novos grupos eles serão cadastrados
if (!is_null($diffErpAtual)){
    foreach($diffErpAtual as $newGrp){
        $reg = explode("|",$newGrp);

        $idErp = $reg[0];
        $groupCode = $reg[1];

        // Tratamento para pegar o id do grupo já cadastrado no IntegraVet
        // com a finalidade de fazer o update efetuando as trocas equivalentes às tabelas
        $searchCode = explode(" ", $groupCode);

        $iSearchCode = end($searchCode);
        $firstWord = $searchCode[0];
        $lastWord = end($searchCode);
        $searchBy = "$firstWord % $lastWord";

        $getIdGroupIntegra = "SELECT customer_group_id FROM ";
        $getIdGroupIntegra .= $resource->getTableName(customer_group);
        $getIdGroupIntegra .= " WHERE customer_group_code LIKE '$searchBy' AND website_id = $websiteId;";

        $idGroupIntegra = $readConnection->fetchOne($getIdGroupIntegra);

        if ($idGroupIntegra == true){
            $updateGroup = "UPDATE ";
            $updateGroup .= $resource->getTableName(customer_group);
            $updateGroup .= " SET customer_group_code = '$groupCode', id_tabela = $idErp";
            $updateGroup .= " WHERE customer_group_id = $idGroupIntegra AND website_id = $websiteId;";

            $writeConnection->query($updateGroup);
        }
    }
    $reg = NULL;
}

// trabalha na união dos grupos

//echo "\n=========================== Trabalhando com a união dos grupos ===================\n";
//$updateGroup = null;
//foreach ($unions as $union) {
//    $lnUnionGrp = count($union);
//    $lnUnionGrp --;
//    $iLnUnionGrp = 0;
//
//    // processa a montagem do nome somente para os grupos com ao menos 3 registros sendo que ultimo sempre será o id da
//    // tabela virtual
//    if ($lnUnionGrp >= 2) {
//        while ($iLnUnionGrp <= $lnUnionGrp) {
//            // Montando o nome da tabela virtual (união da tabelas)
//            foreach ($union as $idErpUnion) {
//                $getIdErp = "SELECT id_tabela FROM ";
//                $getIdErp .= $resource->getTableName(customer_group);
//                $getIdErp .= " WHERE customer_group_id = $union[$iLnUnionGrp] AND website_id = $websiteId AND multiple_table = 0;";
//
//                $idErpUnion = $readConnection->fetchOne($getIdErp);
//
//                if (!empty($idErpUnion)) {
//                    $nameVTable .= $idErpUnion;
//                    if ($iLnUnionGrp < $lnUnionGrp - 1): $nameVTable .= "+"; endif;
//                }
//                $iLnUnionGrp++;
//            }
//
//            $idVTable = end($union); // pega o ultimo ID definido no configIntegra
//            // Atualizando a tabela customer_group
//            // Pego no nome do id_tabela para comparar se há necessidade de fazer atualização
//            $getVTable = "SELECT id_tabela FROM ";
//            $getVTable .= $resource->getTableName(customer_group);
//            $getVTable .= " WHERE customer_group_id = $idVTable AND website_id = $websiteId;";
//
//            $nameVTableAtual = $readConnection->fetchOne($getVTable);
//
//            if ($nameVTableAtual != $nameVTable) {
//
//                // Atualiza o id_Tabela de acordo com a união
//                $updateGroup = "UPDATE ";
//                $updateGroup .= $resource->getTableName(customer_group);
//                $updateGroup .= " SET id_tabela = '$nameVTable'";
//                $updateGroup .= " WHERE customer_group_id = $idVTable AND website_id = $websiteId;";
//
//                $writeConnection->query($updateGroup);
//            }
//
//            $nameVTable = null; // Limpa a variável para a montagem da próxima tabela virtual
//            $getVTable = null; // Limpo a variável para trabalhar com a próxima tabela
//
//            // Trabalhando com a tabela fvets_tableprice
//            // Pega o id)erp da tabela associada a nossa tabela virtual setada no configIntegra
//            $getIdErp = "SELECT id_erp FROM ";
//            $getIdErp .= $resource->getTableName(fvets_tableprice);
//            $getIdErp .= " WHERE customer_group_id = $idVTable";
//
//            echo "\n\n\n $getIdErp \n\n\n";
//
//            $idErp = $readConnection->fetchOne($getIdErp);
//
//            echo "\n\n\n $idErp \n\n\n";
//        }
//    }
//}