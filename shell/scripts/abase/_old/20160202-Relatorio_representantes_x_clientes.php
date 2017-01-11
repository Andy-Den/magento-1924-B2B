<?php
require_once './configScript.php';

$reps = array('325', '317', '320'); //ID 4VETS

$list = array();

$header = false;

foreach ($reps as $rep)
{
	$customers  = Mage::helper('fvets_salesrep')->getSalesRepCustomers($rep);

	$rep = Mage::getModel('fvets_salesrep/salesrep')->load($rep);

	$list[] = array($rep->getName());

	foreach ($customers as $customer)
	{
		$data = $customer->getData();
		if (!$header)
		{
			$list[] = array_keys($data);
			$header = true;
		}
		$list[] = $data;
	}

	$list[] = array('');
	$header = false;
}

$fp = fopen('extras/'.date('Ymd').'-Relatorio_representantes_x_clientes.csv', 'w');

foreach ($list as $fields) {
	fputcsv($fp, $fields);
}

fclose($fp);
