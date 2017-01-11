<?php
require_once './configScript.php';

$product = Mage::getModel('catalog/product');

if (($handle = fopen("extras/premier_policy_group.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
	{
		$id = Mage::getModel('catalog/product')->getResource()->getIdBySku($data[0]);
		if ($id) {
			$product->load($id);
			$product->setPremierPolicyGroup($data[1]);
			$product->getResource()->saveAttribute($product, 'premier_policy_group');
			echo '+';
		}
		else
		{
			echo '-';
		}
	}
}