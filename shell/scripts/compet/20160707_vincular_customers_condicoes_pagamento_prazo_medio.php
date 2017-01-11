<?php
/**
 * Created by PhpStorm.
 * User: fvets
 * Date: 7/7/16
 * Time: 12:57 PM
 */

require_once './configScript.php';

$lines = file("extras/20160707_vincular_customers_condicoes_pagamento_prazo_medio.csv", FILE_IGNORE_NEW_LINES);

$website = Mage::getModel('core/website')->load($websiteId);
$defaultPrazoMedio = 1;
$notFoundedConditions = array();
$notFoundedCustomers = array();

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
        $lineArray = explode('|', $line);
        $customerIdErp = $lineArray[0];
        $prazoMedio = $lineArray[2];
        if (!$prazoMedio) {
            $prazoMedio = $defaultPrazoMedio;
        }

        $customer = getCustomerByIdErp($customerIdErp);

        if ($customer && $customer->getId()) {
            $conditionCustomers[$customerIdErp]['id'] = $customer->getId();
            $conditionCustomers[$customerIdErp]['prazo_medio'] = $prazoMedio;
        } else {
            $notFoundedCustomers[] = $customerIdErp;
        }
        $lineCount++;
    }
}
//==== fim ====

//==== definindo o prazo médio das condições ====
$conditions = $condition = Mage::getModel('fvets_payment/condition')
    ->getCollection()
    ->addStoreFilter($website->getStoreIds());

foreach ($conditions as $condition) {
    $dataInicio = $condition->getStartDays();
    $parcelas = $condition->getSplit();
    $daysBetween = $condition->getSplitRange();

    $prazoMedio = 0;
    for ($i = 0; $i < $parcelas; $i++) {
        if ($i == 0) {
            $prazoMedio = $dataInicio;
            continue;
        }
        $prazoMedio = $prazoMedio + ($dataInicio + ($daysBetween * $i));
    }

    $condition->setPrazoMedio($prazoMedio / $parcelas);
}
//==== fim ====

//==== verificando cada cliente que está no prazo médio ====
foreach ($conditions as $condition) {
    if ($condition->getStartDays() == 1 && $condition->getSplit() == 1) {
        continue;
    }

    $customersDataForCondition = array();
    foreach ($conditionCustomers as $customersData) {
        if ($condition->getprazoMedio() <= $customersData['prazo_medio']) {
            $customersDataForCondition[$customersData['id']] = array('position' => "");
        }
    }

    $condition->setCustomersData($customersDataForCondition);
    $customerRelation = Mage::getModel('fvets_payment/condition_customer');
    $customerRelation->saveConditionRelation($condition);

    echo "+";
}
//==== fim ====

//==== printando os customers não encontrados ====
foreach ($notFoundedCustomers as $notFoundedCustomer) {
    echo "Clientes não encontrados:" ."\n";
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