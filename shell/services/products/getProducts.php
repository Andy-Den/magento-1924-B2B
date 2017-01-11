<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 2/2/16
 * Time: 10:48 AM
 */

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

array_shift($argv);
list($websiteId) = $argv;

if (empty($websiteId)) {
	die('You must provide a website id.');
}

$products = Mage::getModel('catalog/product')->getCollection()
	->addWebsiteFilter($websiteId)
	->addAttributeToSelect('name')
	->addAttributeToSelect('id_erp');

$data = array();
foreach($products as $product) {
	$data[] = array('value' => $product->getIdErp(),
								  'label' => $product->getName());
}
echo json_encode($data);
