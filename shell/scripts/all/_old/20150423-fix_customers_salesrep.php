<?php
require_once './configScript.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/7/15
 * Time: 12:13 PM
 */

$indicesIdSalesRep = array(
	2 => 18,
	4 => 20,
	5 => 19);

foreach ($websitesId as $websiteId)
{
	$lines = file("extras/customers_" . $websiteId . ".csv", FILE_IGNORE_NEW_LINES);
	$firstLine = true;
	$updatedCustomers = 0;
	$customersComProblemasAoAtualizar = 0;
	if (empty($lines))
	{
		echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
	} else
	{
		foreach ($lines as $key => $value)
		{
			if ($firstLine)
			{
				$firstLine = false;
				continue;
			}
			try
			{
				$lines = explode('|', $value);
				$idErp = $lines[1];
				$idErpSalesRep = $lines[$indicesIdSalesRep[$websiteId]];

				$entityIdSalesRep = getSalesrepEntityId($idErpSalesRep, $websiteId);

				if (!$entityIdSalesRep)
				{
					throw new Exception('SalesRep não encontrado!');
				}

				$customer = Mage::getModel('customer/customer')->getCollection()
					->addAttributeToFilter('website_id', $websiteId)
					->addAttributeToFilter('id_erp', $idErp)
					->getFirstItem();

				if (!$customer->getId())
				{
					continue;
				}

				$customer->setFvetsSalesrep($entityIdSalesRep);
				$customer->getResource()->saveAttribute($customer, 'fvets_salesrep');
				$updatedCustomers++;
			} catch (Exception $ex)
			{
				$customersComProblemasAoAtualizar++;
			}
		}
		echo "Website: " . $websiteId . "\nCustomers Atualizados: " . $updatedCustomers . "\nCustomers que deram problema ao atualizar: " . $customersComProblemasAoAtualizar . "\n";
	}
}

function getSalesrepEntityId($idErpSalesRep, $websiteId)
{
	global $readConnection;

	$storeIds = Mage::getModel('core/website')->load($websiteId)->getStoreIds();

	$sql = "SELECT `main_table`.*
FROM `fvets_salesrep` AS `main_table`
WHERE (id_erp = '" . $idErpSalesRep . "') AND (";

	foreach($storeIds as $store)
	{
		$sql .= '(SELECT FIND_IN_SET('.$store.', main_table.store_id)) OR ';
	}

	$sql = substr($sql, 0, strlen($sql)-4).')';

	$entityId = $readConnection->fetchOne($sql);

	return $entityId;
}