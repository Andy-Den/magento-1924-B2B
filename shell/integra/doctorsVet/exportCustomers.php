<?php

require_once './configIntegra.php';
require_once '../_functions/exportCustomers.php';

exportCustomers($websiteId, $storeView);

foreach ($arrayCustomer as $customer) :
    $regCustomers .= $customer['ID'] . "|";
    $regCustomers .= $customer['id_erp'] . "|";
    $regCustomers .= $customer['email'] . "|";
    $regCustomers .= $customer['active'] . "|";
    $regCustomers .= $customer['approved'] . "|";
    $regCustomers .= $customer['id_rep'] . "|";
    $regCustomers .= $customer['first_name'] . ' ' . $customer['last_name'] . "|";
    $regCustomers .= $customer['razao_social'] . "|";
    $regCustomers .= $customer['nome_fantasia'] . "|";
    $regCustomers .= $customer['cnpj'] . "|";
    $regCustomers .= $customer['cpf'] . "|";
    $regCustomers .= $customer['isento'] . "|";
    $regCustomers .= $customer['inscricao_estadual'] . "|";
    $regCustomers .= str_replace("\n", ", ", $customer['endereco']) . "|";
    $regCustomers .= $customer['bairro'] . "|";
    $regCustomers .= $customer['cep'] . "|";
    $regCustomers .= $customer['telefone'] . "\n";
endforeach;


// Gera o arquivo de customers alterados
$currentDate = str_replace("'", "", $currentDate);
if (!is_null($regCustomers)) {
    $outFileStatus = $regCustomersHead . $regCustomers;
    file_put_contents("$directoryExp/customers/customers_$currentDate.csv", $outFileStatus);
    echo "\n Arquivo de customers alterados foi criado.\n";
}