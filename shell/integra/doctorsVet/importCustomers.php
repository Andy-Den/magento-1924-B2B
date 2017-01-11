<?php

require_once './configIntegra.php';
require_once '../_functions/importCustomers.php';
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
$clientes_nao_cadastrados = NULL;

$lines = file("$directoryImp/customers/customers.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) {
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {

    foreach ($lines as $key => $value) {

        $temp[] = str_getcsv($value, '|', "'");

        $tipoPessoa[$key]  =  intval($temp[0]);

    }


    function array_sort_column($column, $pieces, $direction = SORT_ASC)
    {
        $filter = [];
        foreach ($pieces as $key => $value) {
            if (!isset($value[$column])) throw new Exception('Missing column name.');

            $filter[$key] = $value[$column]; // which column
        }
        array_multisort($filter, $direction, $pieces);

        return $pieces;
    }

    $temp = array_sort_column(0,$temp,SORT_DESC);

    foreach ($temp as $key => $value) {
        $i++;
            /* variáveis utilizadas na integração */

        $tipoPessoa = $value[0];
        $idErp = trim($value[1]);
        $name = NULL;
        $razaoSocial = str_replace("'", "\'", $value[2]);
        $fantasia = str_replace("'", "\'", $value[3]);
        $inscricaoEstadual = $value[8];
        $number = $value[10];
        $street = str_replace("'", "\'", $value[9]) . "\n$number";
        $cnpj = $value[4];
        $cpf = $value[5];
        $ramoAtividade = intval($value[6]);
        $telefone = $value[16];
        $bairro = str_replace("'", "\'", $value[12]);
        $cep = $value[13];
        $city = $value[14];
        $state = $value[15];
        $idRep = $value[19];
        $lastOrder = $value[20];
        $idGroup = 10002;
        $emailErp = $value[17];
        $password = '3295e7b503b12d0fa61a1a825e897b37:ZPkM5Hv1sebpf54Z3xkMmzcUHAXGNm0o'; // senha padrão: doctorsvet123
        $status = $value[21];

        if (!$value[17]):
            $emailErp = $value[18];
        endif;

        // Faz o Dê Para dos ramos de atividade da distribuidora para o nosso
        if ($ramoAtividade == 1): $ramoAtividadeIntegra = 4; endif; // Agropecuária
        if ($ramoAtividade == 2): $ramoAtividadeIntegra = 1; endif; // Avicultura
        if ($ramoAtividade == 3): $ramoAtividadeIntegra = NULL; endif; // Funcionário
        if ($ramoAtividade == 4): $ramoAtividadeIntegra = NULL; endif; // Pessoa Física
        if ($ramoAtividade == 5): $ramoAtividadeIntegra = 1; endif; // Pet Shop
        if ($ramoAtividade == 8): $ramoAtividadeIntegra = 2; endif; // Pet Shop/Clinica Veterinária
        if ($ramoAtividade == 9): $ramoAtividadeIntegra = 1; endif; // Canil/Hoteis
        if ($ramoAtividade == 7): $ramoAtividadeIntegra = 2; endif; // Clinica Veterinária/Consultorio
        if ($ramoAtividade == 10): $ramoAtividadeIntegra = 2; endif; // Hospital Veterinario
        if ($ramoAtividade == 11): $ramoAtividadeIntegra = 1; endif; // Banho e tosa
        if ($ramoAtividade == 12): $ramoAtividadeIntegra = 1; endif; // Pet Shop/Banho e tosa
        if ($ramoAtividade == 13): $ramoAtividadeIntegra = 1; endif; // Pet Shop Completo
        if ($ramoAtividade == 14): $ramoAtividadeIntegra = 5; endif; // Distribuidora

        if ($ramoAtividade == 1): $ramoAtividadeIntegra = 4;
        endif; // Agropecuária
        if ($ramoAtividade == 6): $ramoAtividadeIntegra = 1;
        endif; // Pet Shop Completo

        if ($tipoPessoa != 3):

            addCustomer($resource, $readConnection, $writeConnection, $websiteId, $storeId, $storeViewAll, $currentDateFormated, $atualizaEmail, $idGroup, $codeStore, $idErp, $name, $tipoPessoa, $emailErp, $password, $cnpj, $cpf, $ramoAtividadeIntegra, $inscricaoEstadual, $razaoSocial, $fantasia, $telefone, $state, $bairro, $city, $cep, $street, $telefone, $status, $lastOrder, $lastPurchasePrice);
            setGroupAccess($resource, $readConnection, $writeConnection, $currentDateFormated, $websiteId, $storeviewId, $idErp);
            setCustomerGroup($resource, $readConnection, $writeConnection, $currentDateFormated, $websiteId, $storeviewId, $idErp, $idGroup);
            setSalesRep($resource, $readConnection, $writeConnection, $websiteId, $idRep, $idErp, $storeViewReps);
        endif;

        if ($tipoPessoa == 3):
            addCustomer($resource, $readConnection, $writeConnection, $websiteId, $storeId, $storeViewAll, $currentDateFormated, $atualizaEmail, $idGroup, $codeStore, $idErp, $name, $tipoPessoa, $emailErp, $password, $cnpj, $cpf, $ramoAtividadeIntegra, $inscricaoEstadual, $razaoSocial, $fantasia, $telefone, $state, $bairro, $city, $cep, $street, $telefone, $status, $lastOrder, $lastPurchasePrice);
            addSalesRep($directoryRep,$currentDate, $resource, $readConnection, $writeConnection, $idErp, $storeViewReps, $firstName, $lastName, $razaoSocial, $emailErp, $telefone, $storeId);
        endif;

        echo "\n\n Linha >>>>> $i \n\n";
    }
}