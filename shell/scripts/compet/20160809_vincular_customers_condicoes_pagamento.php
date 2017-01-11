<?php
/**
 * Created by PhpStorm.
 * User: fvets
 * Date: 7/7/16
 * Time: 12:57 PM
 */

require_once './configScript.php';

$lines = file("extras/20160809_vincular_customers_condicoes_pagamento.csv", FILE_IGNORE_NEW_LINES);

$website = Mage::getModel('core/website')->load($websiteId);

$notFoundedConditions = array();
$notFoundedCustomers = array();
$arrConditions = array();

$conditionCustomers = array();
$lineCount = 0;

//==== capturando e formatando os clientes do arquivo ====
if (empty($lines)) {
    echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {

    foreach ($lines as $line) {
        if ($lineCount == 0) {
            $lineCount++;
            continue;
        }
        $data = explode('|', $line);
        $arrConditions[$data[0]][] = $data[1];
    }
    
    foreach($arrConditions as $conditionIdErp => $customersIdErp) {
        
        $condition = getConditionByIdErp($conditionIdErp);
        if($condition && $condition->getId()) {
            
            $customersDataForCondition = array();
            foreach($customersIdErp as $customerIdErp){

                $customer = getCustomerByIdErp($customerIdErp);
                if ($customer && $customer->getId()) {
                    $customersDataForCondition[$customer->getId()] = array('position' => getPositionByCondition($conditionIdErp));
                } else {
                    $notFoundedCustomers[] = $customerIdErp;
                }
            }
            $condition->setCustomersData($customersDataForCondition);
            $customerRelation = Mage::getModel('fvets_payment/condition_customer');
            $customerRelation->saveConditionRelation($condition);
            echo "salvos os clientes da condicão id_erp: ". $conditionIdErp."\n";
        } else {
            $notFoundedConditions[] = $conditionIdErp;
        }    
        
 
        $lineCount++;
    }
}
//==== fim ====

//==== printando as condições não encontradas ====
echo "Condições não encontrados:" ."\n";
foreach ($notFoundedConditions as $notFoundedCondition) {
    
    echo $notFoundedCondition . "\n";
}
//==== fim ====

//==== printando os customers não encontrados ====
$notFoundedCustomers = array_unique($notFoundedCustomers);
echo "Clientes não encontrados:" ."\n";
foreach ($notFoundedCustomers as $notFoundedCustomer) {
    
    echo $notFoundedCustomer . "\n";
}
//==== fim ====

function getCustomerByIdErp($idErp)
{
    global $websiteId;

    $customer = Mage::getModel('customer/customer')
        ->getCollection()
        ->addAttributeToFilter('website_id', $websiteId)
        ->addAttributeToFilter('id_erp', $idErp)
        ->getFirstItem();

    return $customer;
}

function getConditionByIdErp($idErp)
{
    global $website;

    $condition = Mage::getModel('fvets_payment/condition')
        ->getCollection()
        ->addFieldToFilter('id_erp', $idErp)
        ->addFieldToFilter('status', 1)
        ->addStoreFilter($website->getStoreIds())
        ->getFirstItem();

    return $condition;
}

function getPositionByCondition($idErp) {

    $positions = [
        3   => 0, // 01 Dia
        4   => 1, // 07 DIAS
        2   => 2, // 07/14 DIAS
        25  => 3, // 07/14/21 DIAS
        14  => 4, // 07/21 DIAS
        16  => 6, // 14/21 DIAS
        6   => 5, // 14 DIAS
        26  => 7, // 14/21/28 DIAS
        29  => 8, // 14/21/28/35 DIAS
        17  => 9, // 14/28 DIAS
        32  => 10, // 14/28/42 DIAS
        38  => 11, // 14/28/42/56 DIAS
        7   => 12, // 21 DIAS
        18  => 13, // 21/28 DIAS
        33  => 14, // 21/28/35 DIAS
        36  => 15, // 21/28/35/42 DIAS
        19  => 16, // 21/35 DIAS
        158 => 17, // 21/35/49 Dias
        20  => 18, // 21/42 DIAS
        1   => 19, // 28 DIAS
        21  => 20, // 28/35 DIAS
        42  => 21, // 28/35/42 DIAS
        44  => 22, // 28/35/42/49 DIAS
        22  => 23, // 28/42 DIAS
        66  => 24, // 35/42 DIAS        
        156 => 25, // CARTÃO CRÉDITO 1X
        157 => 26, // CARTÃO CRÉDITO 2X
        155 => 27, // CARTÃO DÉBITO
    ];

    return $positions[$idErp];
}