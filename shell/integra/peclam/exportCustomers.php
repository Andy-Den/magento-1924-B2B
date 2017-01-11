<?php

require_once './configIntegra.php';

$regCustomersHeadres = 'ID';

$sqlCustomer = "SELECT 
	ce.entity_id AS ID,
	cev.`value` as id_erp, 
	ce.email AS email,
	ce.is_active AS active,
	ce.mp_cc_is_approved AS approved,
	cev1.`value` AS id_rep,
	cev2.`value` AS first_name,
	cev3.`value` AS last_name,
	cev4.`value` AS razao_social,
	cev5.`value` AS nome_fantasia,
	cev6.`value` AS cnpj,
	cev7.`value` AS cpf,
	cei1.`value` AS isento,
	cev8.`value` AS cpf,
	caet1.`value` AS endereco,
	caev1.`value` AS bairro,
	caev2.`value` AS cep,
	caev3.`value` AS cidade,
        dcr.code AS uf,
        caev4.`value` AS telefone
FROM customer_entity AS ce
LEFT JOIN customer_entity_varchar AS cev
ON (ce.entity_id = cev.entity_id) AND cev.attribute_id = 183
LEFT JOIN customer_entity_varchar AS cev1
ON (ce.entity_id = cev1.entity_id) AND cev1.attribute_id = 148
LEFT JOIN customer_entity_varchar AS cev2
ON (ce.entity_id = cev2.entity_id) AND cev2.attribute_id = 5
LEFT JOIN customer_entity_varchar AS cev3
ON (ce.entity_id = cev3.entity_id) AND cev3.attribute_id = 7
LEFT JOIN customer_entity_varchar AS cev4
ON (ce.entity_id = cev4.entity_id) AND cev4.attribute_id = 137
LEFT JOIN customer_entity_varchar AS cev5
ON (ce.entity_id = cev5.entity_id) AND cev5.attribute_id = 199
LEFT JOIN customer_entity_varchar AS cev6
ON (ce.entity_id = cev6.entity_id) AND cev6.attribute_id = 136
LEFT JOIN customer_entity_varchar AS cev7
ON (ce.entity_id = cev7.entity_id) AND cev7.attribute_id = 134
LEFT JOIN customer_entity_int AS cei1
ON (ce.entity_id = cei1.entity_id) AND cei1.attribute_id = 139
LEFT JOIN customer_entity_varchar AS cev8
ON (ce.entity_id = cev8.entity_id) AND cev8.attribute_id = 138
LEFT JOIN customer_address_entity AS cae
ON (ce.entity_id = cae.parent_id)
LEFT JOIN customer_address_entity_text AS caet1
ON (cae.entity_id = caet1.entity_id) AND caet1.attribute_id = 25
LEFT JOIN customer_address_entity_varchar AS caev1
ON (cae.entity_id = caev1.entity_id) AND caev1.attribute_id = 142
LEFT JOIN customer_address_entity_varchar AS caev2
ON (cae.entity_id = caev2.entity_id) AND caev2.attribute_id = 30
LEFT JOIN customer_address_entity_varchar AS caev3
ON (cae.entity_id = caev3.entity_id) AND caev3.attribute_id = 26
LEFT JOIN customer_address_entity_int AS caei
ON (cae.entity_id = caei.entity_id) AND caei.attribute_id = 29
LEFT JOIN directory_country_region AS dcr
ON (caei.`value` = dcr.region_id)
LEFT JOIN customer_address_entity_varchar AS caev4
ON (cae.entity_id = caev4.entity_id) AND caev4.attribute_id = 31
WHERE ce.website_id = $websiteId group by ce.entity_id;";

$arrayCustomer = $readConnection->fetchAll($sqlCustomer);

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
    $regCustomers .= $customer['cidade'] . "|";
    $regCustomers .= $customer['uf'] . "|";
    $regCustomers .= $customer['telefone'] . "\n";
endforeach;


// Gera o arquivo de customers alterados
$currentDate = str_replace("'", "", $currentDate);
if (!is_null($regCustomers)) {
    $outFileStatus = $regCustomersHead . $regCustomers;
    file_put_contents("$directoryExp/customers/customers_$currentDate.csv", $outFileStatus);
    echo "\n Arquivo de customers alterados foi criado.\n";
}
// /exporter/customers
//var_dump($resultCustomer);