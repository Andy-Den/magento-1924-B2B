<?php

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

array_shift($argv);
list($websiteId) = $argv;

if (empty($websiteId)) {
	die('You must provide a website id.');
}

$restrictionGroups = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup')->getCollection()
	->addFieldToFilter('website_id', $websiteId);

$data = array();
foreach ($restrictionGroups as $restrictionGroup) {
	$restrictionGroup->load();
	$data[] = array('value' => $restrictionGroup->getId(),
		'label' => $restrictionGroup->getName());
}
echo json_encode($data);
