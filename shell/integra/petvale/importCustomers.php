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

    foreach ($lines as $key => $value) {
        $i ++;

        $value = str_getcsv($value, '|', "'");

        /* variáveis utilizadas na integração */
        $tipoPessoa = intval($value[0]);
        $idErp = trim($value[1]);
        $idGroup = 1;
        $idRep = $value[3];
        $ramoAtividade = intval($value[4]);
        $name = str_replace("'", "`", $value[5]);
        $razaoSocial = str_replace("'", "`", $value[6]);
        $fantasia = str_replace("'", "`", $value[7]);
        $cnpj = $value[8];
        $cpf = $value[9];
        $isento = $value[10];
        $inscricaoEstadual = $value[11];
        $number = $value[13];
        $complemento = $value[14];
        $street = str_replace("'", "`", $value[12]) . "\n " . $number . "\n" . $complemento;
        $bairro = str_replace("'", "`", $value[15]);
        $cep = $value[16];
        $city = $value[17];
        $state = $value[18];
        $telefone = $value[19];
        $emailErp = strtolower(trim($value[20]));
        $lastOrder = $value[21];
        $lastPurchasePrice = $value[22];
        $password = '048b363ac223755f5126ab41df8469f1:emb9Eve6cZU4NP0tVls1IdjyjbY2i4ww'; // senha padrão: petvale123

        // Faz o Dê Para dos ramos de atividade da distribuidora para o nosso
        if ($ramoAtividade == 1): $ramoAtividadeIntegra = 4;
        endif; // Agropecuária
        if ($ramoAtividade == 2): $ramoAtividadeIntegra = 1;
        endif; // Avicultura
        if ($ramoAtividade == 3): $ramoAtividadeIntegra = NULL;
        endif; // Funcionário
        if ($ramoAtividade == 4): $ramoAtividadeIntegra = NULL;
        endif; // Pessoa Física
        if ($ramoAtividade == 5): $ramoAtividadeIntegra = 1;
        endif; // Pet Shop
        if ($ramoAtividade == 8): $ramoAtividadeIntegra = 2;
        endif; // Pet Shop/Clinica Veterinária
        if ($ramoAtividade == 9): $ramoAtividadeIntegra = 1;
        endif; // Canil/Hoteis
        if ($ramoAtividade == 7): $ramoAtividadeIntegra = 2;
        endif; // Clinica Veterinária/Consultorio
        if ($ramoAtividade == 10): $ramoAtividadeIntegra = 2;
        endif; // Hospital Veterinario
        if ($ramoAtividade == 11): $ramoAtividadeIntegra = 1;
        endif; // Banho e tosa
        if ($ramoAtividade == 12): $ramoAtividadeIntegra = 1;
        endif; // Pet Shop/Banho e tosa
        if ($ramoAtividade == 13): $ramoAtividadeIntegra = 1;
        endif; // Pet Shop Completo
        if ($ramoAtividade == 14): $ramoAtividadeIntegra = 5;
        endif; // Distribuidora

        if ($tipoPessoa != 3):
            addCustomer($resource, $readConnection, $writeConnection, $websiteId, $storeId, $storeViewAll, $currentDateFormated, $atualizaEmail, $idGroup, $codeStore, $idErp, $name, $tipoPessoa, $emailErp, $password, $cnpj, $cpf, $ramoAtividadeIntegra, $inscricaoEstadual, $razaoSocial, $fantasia, $telefone, $state, $bairro, $city, $cep, $street, $telefone, $status, $lastOrder, $lastPurchasePrice);
            setGroupAccess($resource, $readConnection, $writeConnection, $currentDateFormated, $websiteId, $storeviewId, $idErp);
            setCustomerGroup($resource, $readConnection, $writeConnection, $currentDateFormated, $websiteId, $storeviewId, $idErp, $idGroup);
            setSalesRep($resource, $readConnection, $writeConnection, $websiteId, $idRep, $idErp, $storeViewReps);

        elseif ($tipoPessoa == 3):
            addCustomer($resource, $readConnection, $writeConnection, $websiteId, $storeId, $storeViewAll, $currentDateFormated, $atualizaEmail, $idGroup, $codeStore, $idErp, $name, $tipoPessoa, $emailErp, $password, $cnpj, $cpf, $ramoAtividadeIntegra, $inscricaoEstadual, $razaoSocial, $fantasia, $telefone, $state, $bairro, $city, $cep, $street, $telefone, $status, $lastOrder, $lastPurchasePrice);
            addSalesRep($directoryRep,$currentDate, $resource, $readConnection, $writeConnection, $idErp, $storeViewReps, $firstName, $lastName, $razaoSocial, $emailErp, $telefone, $storeId);
        endif;

        echo "\n\n Linha >>>>> $i \n\n";
    }
}