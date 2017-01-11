<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/9/15
 * Time: 10:19 AM
 */

require_once './configScript.php';


$restriction_groups = array();

if (($handle = fopen("extras/20160226-adiciona_clientes_e_produtos_com_restricao.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

		if (!isset($restriction_groups[$data[0]]))
		{
			$restriction_groups[$data[0]] = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup')->load($data[0]);
		}

		$group = $restriction_groups[$data[0]];

		if ($group->getId())
		{
			echo 'p';
			$products = explode(',', $data[1]);
			$post = array();
			foreach ($products as $product)
			{
				$product = Mage::getModel('catalog/product')
					->getCollection()
					->addAttributeToFilter('sku', $product)
					->getFirstItem();

				if ($product->getId())
				{
					$post[$product->getId()] = '0';
				}

				echo '.';
			}

			$catalogrestrictiongroupProduct = Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_product')
				->saveCatalogrestrictiongroupRelation($group, $post);

			echo 'c';
			$customers = explode(',', $data[2]);
			$post = array();
			foreach($customers as $customer)
			{
				$post[$customer] = '0';
				echo '.';
			}

			$catalogrestrictiongroupCustomer = Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_customer')
				->saveCatalogrestrictiongroupRelation($group, $post);

		}

		echo '|';
	}
	fclose($handle);
}


echo "\n";
echo 'Bye';