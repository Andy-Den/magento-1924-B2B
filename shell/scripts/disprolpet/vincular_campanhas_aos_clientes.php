<?php
/**
 * Created by PhpStorm.
 * User: fvets
 * Date: 7/5/16
 * Time: 9:50 AM
 */

require_once './configScript.php';

$lines = file("extras/20160815_vincular_campanhas_aos_clientes.csv", FILE_IGNORE_NEW_LINES);

$notFoundedCustomers = array();

if (empty($lines)) {
    echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
    exit;
} else {
    $firstLine = true;
    foreach ($lines as $line) {
        if ($firstLine) {
            $firstLine = false;
            continue;
        }
        $data = explode('|', $line);
        $customerIdErp = array_shift($data);

        $arrayRules = array();
        foreach ($data as $rule_id) {
            if (!$rule_id) {
                continue;
            }
            $arrayRules[$rule_id] = array();
        }

        $customer = getCustomerByIdErp($customerIdErp);
        if ($customer && $customer->getId()) {
            Mage::getResourceSingleton('fvets_salesrule/salesrule_customer')->saveCustomerRelation($customer, $arrayRules);
            echo "Salva as campanhas para o cliente ".$customerIdErp."\n";
        } else {
            $notFoundedCustomers[] = $customerIdErp;
        }
    }
}

if (count($notFoundedCustomers) > 0) {
    echo "Customers não encontrados: " . count($notFoundedCustomers) . "\n";
    foreach ($notFoundedCustomers as $notFoundedCustomer) {
        echo $notFoundedCustomer . "\n";
    }
}

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