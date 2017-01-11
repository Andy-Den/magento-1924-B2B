<?php
require_once './configScript.php';

$lines = file("extras/20160620_abase_customers.csv", FILE_IGNORE_NEW_LINES);

$brandsIdToIgnore = array('zoetis' => 706, 'bayer' => 702);
$restrictionGroupId = array('zoetis_eticos' => 3);

$firstLine = true;
$lineCount = 1;

$fileArray = array();
if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
	exit;
} else {
	foreach ($lines as $line) {
		if ($firstLine) {
			$firstLine = false;
			$lineCount++;
			continue;
		}

		$fileArrayTmp = array();
		$fileArrayTmp[] = explode('|', $line);
		if ($fileArrayTmp[0][4]) {
			$fileArray[] = array('ativo' => $fileArrayTmp[0][0], 'zoetis' => $fileArrayTmp[0][1], 'zoetis_eticos' => $fileArrayTmp[0][2], 'bayer' => $fileArrayTmp[0][3], 'id_erp' => $fileArrayTmp[0][4]);
		} else {
			echo "\n\n" . 'Arquivo sem formatação válida: "código do cliente" deve ser preenchido (linha ' . $lineCount . ')' . "\n\n";
		}
		$lineCount++;
	}
}

$lines = file("extras/20160523_abase_bounces.csv", FILE_IGNORE_NEW_LINES);
$fileBouncesArray = array();
if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
	exit;
} else {
	foreach ($lines as $line) {
		$fileBouncesArray[] = $line;
	}
}

//echo "\n" . "desativando todos os customers" . "\n";
//inactiveAllCustomers();
echo "\n" . "atualiza status dos customers de acordo com arquivo" . "\n";
foreach ($fileArray as $item) {
	if ($item['ativo'] == 'SIM') {
		updateStatusCustomer($item['id_erp'], false);
	} else {
		updateStatusCustomer($item['id_erp'], true);
	}
}
echo "\n" . "atualiza restrição de marca" . "\n";
foreach ($fileArray as $item) {
	if ($item['zoetis'] == 'SIM') {
		updateBrandRestriction($item['id_erp'], $brandsIdToIgnore['zoetis'], false);
	} else {
		updateBrandRestriction($item['id_erp'], $brandsIdToIgnore['zoetis'], true);
	}

	if ($item['bayer'] == 'SIM') {
		updateBrandRestriction($item['id_erp'], $brandsIdToIgnore['bayer'], false);
	} else {
		updateBrandRestriction($item['id_erp'], $brandsIdToIgnore['bayer'], true);
	}
}
echo "\n" . "atualiza grupo de restrição" . "\n";
foreach ($fileArray as $item) {
	if ($item['zoetis_eticos'] == 'SIM') {
		updateCustomerRestrictionGroup($item['id_erp'], $restrictionGroupId['zoetis_eticos'], false);
	} else {
		updateCustomerRestrictionGroup($item['id_erp'], $restrictionGroupId['zoetis_eticos'], true);
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
	global $fileBouncesArray;

	// se não for uma instância de customer, deve ser um ID_ERP, então loadar esse cara:
	if (is_string($customer)) {
		$customer = Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addAttributeToFilter(
				array(
					array('attribute' => 'id_erp', 'eq' => str_pad($customer, 6, "0", STR_PAD_LEFT)),
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
			//verificar antes se o cliente está na lista de bounces. Caso esteja, desativá-lo apenas no allin;
			if (!in_array($customer->getEmail(), $fileBouncesArray)) {
				$customer->setData('fvets_allin_status', 'V');
				$customer->setBlockAllinStatus(0);
			} else {
				$customer->setData('fvets_allin_status', 'I');
				$customer->setBlockAllinStatus(1);
			}
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