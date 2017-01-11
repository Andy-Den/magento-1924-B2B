<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 3/15/16
 * Time: 3:54 PM
 */

require_once './configScript.php';

$websites = Mage::getModel('core/website')->getCollection()
	->addFieldToFilter('code', array('nin' => array('base', 'admin')));

foreach ($websites as $website)
{
	$data = '';
	$products = Mage::getModel('catalog/product')
		->getCollection()
		->addWebsiteFilter($website);

	echo "### " . $website->getCode() . " ###";

	foreach ($products as $product)
	{
		$product->setStoreId(reset($website->getStoreIds()))->load();
		//$product->load();
		if ($product->getIdErp())
		{
			$data .= ($product->getIdErp() . '|' . $product->getName() . "\n");
		}
	}

	file_put_contents('extras' . DS . $website->getCode(), $data);
}