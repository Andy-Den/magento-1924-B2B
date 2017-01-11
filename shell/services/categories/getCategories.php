<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 2/2/16
 * Time: 10:48 AM
 */

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

$marcasCategoryId = 915;
$brandsLevel = 2;

$categories = Mage::getModel('catalog/category')->getCollection()
	//->addFieldToFilter('parent_id', $marcasCategoryId)
	->addFieldToFilter('level', $brandsLevel)
	->setOrder('name', 'asc');

$data = array();
foreach ($categories as $category) {
	$category->load();
	$data[] = array('value' => $category->getId(),
		'label' => $category->getName() . ' [' . $category->getParentCategory()->getName() . ']');
}
echo json_encode($data);
