<?php
require_once './configScript.php';

$lines = file("extras/20160509_abase_atualizacao_customers_planilha.csv", FILE_IGNORE_NEW_LINES);

$brandIdToIgnore = 706;
$restrictionGroupId = 3;

$firstLine = true;

$fileArray = array();
if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	foreach ($lines as $line) {
		if ($firstLine) {
			$firstLine = false;
			continue;
		}

//		$abaseOnline = explode('|', $line)[0];
//		$zoetis = explode('|', $line)[1];
//		$zoetisEticos = explode('|', $line)[2];
//		$customerIdErp = explode('|', $line)[3];

		$fileArray[] = explode('|', $line);
	}
}

echo "\n" . "desativando todos os customers" . "\n";
//inactiveAllCustomers();
echo "\n" . "atualiza status dos customers de acordo com arquivo" . "\n";
foreach ($fileArray as $item) {
	if ($item[0] == 'SIM') {
		updateStatusCustomer($item[3], false);
	} else {
		updateStatusCustomer($item[3], true);
	}
}
echo "\n" . "atualiza restrição de marca" . "\n";
foreach ($fileArray as $item) {
	if ($item[1] == 'SIM') {
		updateBrandRestriction($item[3], $brandIdToIgnore, false);
	} else {
		updateBrandRestriction($item[3], $brandIdToIgnore, true);
	}
}
echo "\n" . "atualiza grupo de restrição" . "\n";
foreach ($fileArray as $item) {
	if ($item[2] == 'SIM') {
		updateCustomerRestrictionGroup($item[3], $restrictionGroupId, false);
	} else {
		updateCustomerRestrictionGroup($item[3], $restrictionGroupId, true);
	}
}

function inactiveAllCustomers()
{
	global $websiteId;

	$customers = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('website_id', $websiteId);

	foreach ($customers as $customer) {
		$customer->load();
		updateStatusCustomer($customer);
	}
}

function updateStatusCustomer($customer, $inactive = true)
{
	global $websiteId;

	// se não for uma instância de customer, deve ser um ID_ERP, então loadar esse cara:
	if (is_string($customer)) {
		$customer = Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addAttributeToFilter(
				array(
					array('attribute' => 'id_erp', 'eq' => '00' . $customer),
					array('attribute' => 'id_erp', 'eq' => $customer)
				)
			)
			->getFirstItem();
	}

	//se ele existe:
	if ($customer && $customer->getId()) {
		//caso seja para inativá-lo:
		if ($inactive) {
			$customer->setData('fvets_allin_status', 'I');
			$customer->setBlockAllinStatus(1);
			$customer->setIsActive(0);
			$customer->save();
		} else {
			//caso seja para ativá-lo:
			$customer->setData('fvets_allin_status', 'V');
			$customer->setBlockAllinStatus(0);
			$customer->setIsActive(1);
			$customer->save();
		}
		echo '+';
	} else {
	}
}

function updateBrandRestriction($customerIdErp, $brandId, $restricted = true)
{
	global $websiteId;

	//loadar esse customer pelo ID_ERP:
	$customer = Mage::getModel('customer/customer')
		->getCollection()
		->addAttributeToFilter('website_id', $websiteId)
		->addAttributeToFilter(
			array(
				array('attribute' => 'id_erp', 'eq' => str_pad($customerIdErp, 6, "0", STR_PAD_LEFT)),
				array('attribute' => 'id_erp', 'eq' => $customerIdErp)
			)
		)
		->getFirstItem();

	//se ele existe:
	if ($customer->getId()) {
		$customer->load();
		//se o customer não tem nenhuma restrição e deveria, então:
		if (!$customer->getRestrictedBrands() && $restricted) {
			$customer->setRestrictedBrands($brandId);
			$customer->getResource()->saveAttribute($customer, 'restricted_brands');
			echo '+';
		} else {
			$customerRestrictedBrands = $customer->getRestrictedBrands();
			$customerRestrictedBrands = explode(',', $customerRestrictedBrands);

			//se o customer não possui a restrição da marca e deveria, então:
			if (!in_array($brandId, $customerRestrictedBrands) && $restricted) {
				$customerRestrictedBrands[] = (string)$brandId;
				$customer->setRestrictedBrands(implode(',', $customerRestrictedBrands));
				$customer->getResource()->saveAttribute($customer, 'restricted_brands');
				echo '+';
			} else {
				//se o customer possui a restrição da marca e NÃO deveria, então:
				if (in_array($brandId, $customerRestrictedBrands) && !$restricted) {
					foreach ($customerRestrictedBrands as $key => $value) {
						if ($brandId == $value) {
							unset($customerRestrictedBrands[$key]);
							break;
						}
					}
					$customer->setRestrictedBrands(implode(',', $customerRestrictedBrands));
					$customer->getResource()->saveAttribute($customer, 'restricted_brands');
					echo '+';
				}
			}
		}
	}
}

function updateCustomerRestrictionGroup($customerIdErp, $groupId, $restricted = true)
{
	global $websiteId;

	//loadar esse cara pelo ID_ERP:
	$customer = Mage::getModel('customer/customer')
		->getCollection()
		->addAttributeToFilter('website_id', $websiteId)
		->addAttributeToFilter(
			array(
				array('attribute' => 'id_erp', 'eq' => str_pad($customerIdErp, 6, "0", STR_PAD_LEFT)),
				array('attribute' => 'id_erp', 'eq' => $customerIdErp)
			)
		)
		->getFirstItem();

	//se ele existe:
	if ($customer->getId()) {
		$customer->load();
		//atualiza o grupo de restrição desse cara:
		Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_customer')->saveCustomerRelation($customer, $restricted ? array($groupId => array()) : null);
		echo "+";
	}
}