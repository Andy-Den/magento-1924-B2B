<?php

require_once './configScript.php';

$lines = file("extras/20160519-definir_marca_bayer_restrita_customers.csv", FILE_IGNORE_NEW_LINES);

$brandIdToIgnore = 702;

if (empty($lines))
{
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else
{
	foreach ($lines as $customerIdErp)
	{
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

		if ($customer->getId())
		{
			$customer->load();
			if (!$customer->getRestrictedBrands())
			{
				$customer->setRestrictedBrands($brandIdToIgnore);
				$customer->getResource()->saveAttribute($customer, 'restricted_brands');
				echo '+';
			} else
			{
				$customerRestrictedBrands = $customer->getRestrictedBrands();
				$customerRestrictedBrands = explode(',', $customerRestrictedBrands);

				if (!in_array($brandIdToIgnore, $customerRestrictedBrands))
				{
					$customerRestrictedBrands[] = $brandIdToIgnore;
					$customer->setRestrictedBrands(implode(',', $customerRestrictedBrands));
					$customer->getResource()->saveAttribute($customer, 'restricted_brands');
					echo '<>';
				} else {
					echo '[]';
				}
			}
		} else {
			echo '-';
		}
	}
}