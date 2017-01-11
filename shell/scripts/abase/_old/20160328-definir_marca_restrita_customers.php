<?php

require_once './configScript.php';

$lines = file("extras/20160328-restringir_zoetis_customers.csv", FILE_IGNORE_NEW_LINES);

$brandIdToIgnore = 706;

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
					array('attribute' => 'id_erp', 'eq' => '00' . $customerIdErp),
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
				}
				echo '<>';
			}
		}
	}
}