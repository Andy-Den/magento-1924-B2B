<?php 
require_once './configScript.php';

$lines = file("extras/20160830-atualizacao-email-clientes.csv", FILE_IGNORE_NEW_LINES);

$notFoundedCustomers = array();
$totalLines = 1;
$erros = [];

if (empty($lines)) {
    echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
    exit;
} else {
	$firstLine = true;
    foreach ($lines as $line) {
        if ($firstLine) {
            $firstLine = false;
            $totalLines++;
            continue;
        }
        $data = explode('|', $line);
        list($emailNovo, $websiteId, $customerIdErp, $emailOriginal) = $data;

        if(!$customerIdErp && !$emailOriginal) {
        	$erros[] = "Não tem e-mail e nem id_erp associado na linha $totalLines";
        	$totalLines++;
        	continue;
        }
        
        

        if(!$customerIdErp) {
        	$customer = getCustomerByEmail($websiteId, $emailOriginal);
        } else {
        	$customer = getCustomerByIdErp($websiteId, $customerIdErp);
        }        

        if($customer->getId()) {
        	$customer->load();

        	try {
        		$customer->setEmail(trim($emailNovo));
        		$customer->save();
        		
        	} catch (Exception $e) {
        		$erros[] = "Houve um erro ao salvar o cliente da linha $totalLines. Motivo: ".$e->getMessage();
        	}

        } else {
        	$erros[] = "Cliente não encontrado na linha $totalLines";
        }
        
		$totalLines++;
    }

    if(!empty($erros)){
    	foreach($erros as $erro) {
    		echo $erro."\n";
    	}
    }    
}

function getCustomerByIdErp($websiteId,$idErp)
{    

    $customer = Mage::getModel('customer/customer')
        ->getCollection()
        ->addAttributeToFilter('website_id', $websiteId)
        ->addAttributeToFilter('id_erp', $idErp)
        ->getFirstItem();

    return $customer;
}

function getCustomerByEmail($websiteId,$email)
{    

    $customer = Mage::getModel('customer/customer')
        ->getCollection()
        ->addAttributeToFilter('website_id', $websiteId)
        ->addAttributeToFilter('email', $email)
        ->addAttributeToSelect('id_erp')
        ->getFirstItem();

    return $customer;
}