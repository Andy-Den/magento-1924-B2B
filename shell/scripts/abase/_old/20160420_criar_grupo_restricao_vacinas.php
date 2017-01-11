<?php
/**
 * Created by PhpStorm.
 * User: ianitsky
 * Date: 2/18/16
 * Time: 5:37 PM
 */
require_once './configScript.php';

$storeId = 16;

$line = 0;
if (($handle = fopen("extras/20160420_criar_grupo_restricao_vacinas.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

		if ($line++ == 0)
		{
			continue;
		}

		$restriction_group = $data[0];
		$restriction_group = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup')->load($restriction_group);

		if (!$restriction_group->getId())
		{
			continue;
		}

		$products = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToFilter('sku', array('in' => explode(',', $data[2])));

		$restriction_data = array();
		foreach ($products->getAllIds() as $id)
		{
			$restriction_data[$id] = array('position' => '0');
		}

		Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_product')->saveCatalogrestrictiongroupRelation($restriction_group, $restriction_data);

		$customers = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('website_id', $websiteId)
			->addAttributeToFilter('id_erp', array('in' => explode(',', $data[3])));

		$restriction_data = array();
		foreach ($customers->getAllIds() as $id)
		{
			$restriction_data[$id] = array('position' => '0');
		}

		Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_customer')->saveCatalogrestrictiongroupRelation($restriction_group, $restriction_data);

	}
	fclose($handle);
}