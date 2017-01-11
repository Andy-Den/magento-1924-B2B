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


$lines = file("$directoryImp/customers/customers.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {

    $clientes_nao_cadastrados = NULL;

    foreach ($lines as $key => $value) {
        $i++;
        $temp = str_getcsv($value, '|', "'");

        /* variáveis utilizadas na integração */
        $idErp = trim($temp[1]);
        $razaoSocial = str_replace("'", "`", $temp[3]);
        $fantasia = str_replace("'", "`", $temp[4]);
        $cnpj = $temp[5];
        $cpf = $temp[6];
        $ramoAtividade = intval($temp[7]);
        $idGroup = $temp[8];
        $isento = $temp[9];
        $inscricaoEstadual = $temp[10];
        $number = $temp[12];
        $street = str_replace("'", "`", $temp[11]) . ", " . $number;
        $complemento = $temp[13];
        $bairro = str_replace("'", "`", $temp[14]);
        $cep = $temp[15];
        $city = $temp[16];
        $state = $temp[17];
        $telefone = $temp[18];
        $emailErp = strtolower(trim($temp[19]));
        $idRep = $temp[20];
        $lastOrder = $temp[21];

        /* Coloca o nome no padrão do Magento */
        $names = explode(' ', $temp[2], 2);
        $firstName = str_replace("'", "`", $names[0]);
        $lastName = str_replace("'", "`", $names[1]);
        if (empty($first_name)): $first_name = ' ';
        else: $first_name = $names[0]; endif;

        /* define o tipo de negócio do cliente */
        if ($temp[0] == 1):
            $tipoPessoa = "PF";
        elseif ($temp[0] == 2):
            $tipoPessoa = 'PJ';
        elseif ($temp[0] == 3):
            $tipoPessoa = 'RC';
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

        if ($tipoPessoa != 3):
            addCustomer($resource, $readConnection, $writeConnection, $websiteId, $storeId, $storeViewAll, $currentDateFormated, $atualizaEmail, $idGroup, $codeStore, $idErp, $name, $tipoPessoa, $emailErp, $password, $cnpj, $cpf, $ramoAtividadeIntegra, $inscricaoEstadual, $razaoSocial, $fantasia, $telefone, $state, $bairro, $city, $cep, $street, $telefone, $status, $lastOrder, $lastPurchasePrice);
            setGroupAccess($resource, $readConnection, $writeConnection, $currentDateFormated, $websiteId, $storeviewId, $idErp);
            setCustomerGroup($resource, $readConnection, $writeConnection, $currentDateFormated, $websiteId, $storeviewId, $idErp, $idGroup);
            setSalesRep($resource, $readConnection, $writeConnection, $websiteId, $idRep, $idErp, $storeViewReps);

        elseif ($tipoPessoa == 3):
            addCustomer($resource, $readConnection, $writeConnection, $websiteId, $storeId, $storeViewAll, $currentDateFormated, $atualizaEmail, $idGroup, $codeStore, $idErp, $name, $tipoPessoa, $emailErp, $password, $cnpj, $cpf, $ramoAtividadeIntegra, $inscricaoEstadual, $razaoSocial, $fantasia, $telefone, $state, $bairro, $city, $cep, $street, $telefone, $status, $lastOrder, $lastPurchasePrice);
            addSalesRep($directoryRep, $currentDate, $resource, $readConnection, $writeConnection, $idErp, $storeViewReps, $firstName, $lastName, $razaoSocial, $emailErp, $telefone, $storeId);
        endif;

        echo "\n\n Linha >>>>> $i \n\n";
    }
}