<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 2/2/16
 * Time: 2:07 PM
 */

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

require_once $pathBase . 'lib/FVets/ImportPromotions/Abstracts/Controller.php';
require_once $pathBase . 'lib/FVets/ImportPromotions/Controllers/Importer.php';
require_once $pathBase . 'lib/FVets/ImportPromotions/Aux/Functions.php';
require_once $pathBase . 'lib/FVets/ImportPromotions/Aux/Constants.php';
require_once $pathBase . 'lib/FVets/ImportPromotions/Interfaces/ICampaign.php';
require_once $pathBase . 'lib/FVets/ImportPromotions/Abstracts/Campaign.php';
require_once $pathBase . 'lib/FVets/ImportPromotions/Entity/CampaignType1.php';

array_shift($argv);
list($data) = $argv;

if (empty($data))
{
	die('You must provide a campaign data.');
}

$data = json_decode($data);
$data = objectToArray($data);

$data['website_code'] = Mage::getModel('core/website')->load($data['website_id'])->getCode();
$data['prioridade'] = 1;

var_dump($data);

$campaignController = new Controllers_Importer();
$campaign = $campaignController->initCampaign($data);
$campaignController->importCampaign($campaign);

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}
