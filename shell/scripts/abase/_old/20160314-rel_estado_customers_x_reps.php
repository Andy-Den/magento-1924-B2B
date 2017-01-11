<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 8/4/15
 * Time: 4:41 PM
 */

require_once './configScript.php';

$website = Mage::getModel('core/website')->load($websiteId);

$reps = Mage::getModel('fvets_salesrep/salesrep')->getCollection()
	->addFieldToFilter('store_id', array('in' => $website->getStoreIds()));

foreach ($reps as $rep)
{
	$customersAtivos = 0;
	$customersAllinAtivos = 0;
	$customersAllinInativos = 0;

	$customers = $rep->getSelectedCustomersCollection();

	echo "##### " . $rep->getName() . " #####" . "\n";

	foreach ($customers as $customer)
	{
		$customer->load();
		if ($customer->getIsActive())
		{
			$customersAtivos++;
		}
		if ($customer->getFvetsAllinStatus())
		{

			if ($customer->getFvetsAllinStatus() == 'V')
			{
				$customersAllinAtivos++;
			} else
			{
				$customersAllinInativos++;
			}
		}
	}
	echo "Customers ativos: " . $customersAtivos . "\n";
	echo "Customers Allin Ativos: " . $customersAllinAtivos . "\n";
	echo "Customers Allin Inativos: " . $customersAllinInativos;
	echo "\n\n\n";
}
