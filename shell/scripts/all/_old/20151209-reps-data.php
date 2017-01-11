<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/9/15
 * Time: 10:19 AM
 */

require_once './configScript.php';

$websites = array(2, 4, 5, 6, 8, 9, 10, 11, 12, 13);

$finalData = '';

foreach ($websites as $website)
{
	$website = Mage::getModel('core/website')->load($website);

	echo "|";
	$finalData .= $website->getName() . "\n";

	$stores = $website->getStoreCollection();

	$reps = array();

	foreach ($stores as $store)
	{
		$tmp = Mage::getModel('fvets_salesrep/salesrep')->getCollection()
			->addFieldToFilter('store_id', array('finset' => array($store->getStoreId())));

		foreach ($tmp as $rep)
		{
			$reps[$rep->getId()] = $rep;
		}
	}

	//echo $reps->getSelect()->__toString();

	foreach ($reps as $rep)
	{
		$rep = Mage::getModel('fvets_salesrep/salesrep')->load($rep->getId());
		$cats = Mage::helper('fvets_salesrep/category')->getRepBrands($rep);

		$finalData .= $rep->getName() . '|' . $rep->getIdErp() . '|' . $rep->getEmail() . '|' . $rep->getTelephone();

		foreach ($cats as $cat)
		{
			$finalData .= ('|' . $cat->getName());
		}

		$finalData .= "\n";
		echo '+';
	}

	$finalData .= "\n\n";

}

echo "\n";

//$channel = 'scripts';
$channel = 'test';

Mage::helper('datavalidate')->createChannel($channel);

$filename = date('Ymd').'-SalesrepReport.csv';
$data = $finalData;

if (file_put_contents('extras'. DS .$filename, $data))
{
	Mage::helper('datavalidate')->sendSlackFile("Relatório de Representantes", Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'all' . DS . 'extras' . DS . $filename, 'csv', $channel);
}
else
{
	echo "Arquivo" . $filename . " não salvo" . "\n";
}

echo "\n";
echo 'Bye';