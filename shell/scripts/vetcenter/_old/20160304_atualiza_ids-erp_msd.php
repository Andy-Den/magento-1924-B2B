<?php
/**
 * Created by PhpStorm.
 * User: ianitsky
 * Date: 2/18/16
 * Time: 5:37 PM
 */
require_once './configScript.php';

$lines = file("extras/msd.csv", FILE_IGNORE_NEW_LINES);
$storeId = 18;

if (empty($lines))
{
	echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else
{
	foreach ($lines as $key => $value)
	{
		$value = explode('|', $value);

		$sku = $value[0];
		$idErp = $value[1];

		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

		if (!$product) {
			echo "-" . $sku . "\n";
			continue;
		}

		$product->setData('id_erp', $idErp);
		$product->getResource()->saveAttribute($product,'id_erp');

		echo "+" . $product->getSku() . "\n";
	}
}