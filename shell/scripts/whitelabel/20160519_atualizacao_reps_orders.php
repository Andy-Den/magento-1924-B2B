<?php
require_once './configScript.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/19/16
 * Time: 10:38 AM
 */

$items = Mage::getModel('sales/order_item')->getCollection();
$helper = Mage::helper('fvets_salesrep');
$toUpdateSalesrepId = null;
$customersSemRep = array();
$customersCujoRepNaoVendeOItem = array();
foreach ($items as $item) {
	if (!$item->getSalesrepId()) {
		$customer = Mage::getModel('customer/customer')->load(Mage::getModel('sales/order')->load($item->getOrderId())->getCustomerId());
		if ($customer->getId()) {
			$salesrepIds = $customer->getFvetsSalesrep();
			if ($salesrepIds) {
				$salesrepsIdsArray = explode(',', $salesrepIds);
				$toUpdateSalesrepId = null;
				foreach ($salesrepsIdsArray as $salesrepsId) {
					if ($helper->canSellProduct($salesrepsId, $item->getProductId())) {
						$toUpdateSalesrepId = $salesrepsId;
						break;
					} else {
						$customersCujoRepNaoVendeOItem[] = $customer->getId() . "|" . $customer->getIdErp() . "|" . $customer->getName() . "|" . $customer->getEmail();
					}
				}
				if ($toUpdateSalesrepId) {
					$item->setSalesrepId($toUpdateSalesrepId);
					$item->save();
					echo "+";
				}
			} else {
				$customersSemRep[] = $customer->getId() . "|" . $customer->getIdErp() . "|" . $customer->getName() . "|" . $customer->getEmail();
			}
		}
	}
}

echo "Quantidade de itens cujo customer não tem representantes: " . count($customersSemRep) . "\n";
printFile('customersSemRep', implode("\n", $customersSemRep));
echo "Quantidade de customers que não vendem o item: " . count($customersCujoRepNaoVendeOItem) . "\n";
printFile('customersCujoRepNaoVendeOItem', implode("\n", $customersCujoRepNaoVendeOItem));
function printFile($fileName, $data)
{
	global $websiteId;
	$myfile = fopen('extras/' . date('Ymd') . "_" . $fileName . "_" . $websiteId . ".csv", "w") or die("Unable to open file!");
	fwrite($myfile, $data);
	fclose($myfile);
}



