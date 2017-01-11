<?php

require_once './configScript.php';

$lines = file("extras/mixpet_customers.csv", FILE_IGNORE_NEW_LINES);
$firstLine = true;
$customerSemRepVinculado = 0;
$customerComRepErrado = 0;
$customersNaoEncontrados = 0;
$customersComRepNaoCadastrado = 0;
if (empty($lines))
{
	echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else
{
	foreach ($lines as $key => $value)
	{
		$idErpCustomer = explode('|', $value)[0];
		$idErpRep = explode('|', $value)[1];
		//echo $idErpCustomer . "|" . $idErpRep . "\n";

		$customer = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('id_erp', $idErpCustomer)
			->getFirstItem();

		if ($customer->getId())
		{
			$customer->load();
			//echo $customer->getFvetsSalesrep() . "\n";
			if (!$customer->getFvetsSalesrep())
			{
				$customerSemRepVinculado++;
			} else
			{
				$fvetsSalesrep = Mage::getModel('fvets_salesrep/salesrep')->load($customer->getFvetsSalesrep());
				if ($fvetsSalesrep && $fvetsSalesrep->getId())
				{
					if ($fvetsSalesrep->getIdErp() != $idErpRep)
					{
						$customerComRepErrado++;
						echo $customer->getId() . "|" . $customer->getEmail() . "\n";
					}
				} else {
					$customersComRepNaoCadastrado++;
				}
			}
		} else
		{
			$customersNaoEncontrados++;
		}
	}

	echo "customerSemRepVinculado: " . $customerSemRepVinculado . "\n";
	echo "customerComRepErrado: " . $customerComRepErrado . "\n";
	echo "customersNaoEncontrados: " . $customersNaoEncontrados . "\n";
	echo "customersComRepNaoCadastrado: " . $customersComRepNaoCadastrado . "\n";
}