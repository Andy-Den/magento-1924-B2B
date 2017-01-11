<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/13/15
 * Time: 5:11 PM
 */

require_once './configScript.php';

$codesWebsites = array(5 => 'doctorsVet', 6 => 'petvale');

foreach ($codesWebsites as $key => $codesWebsite)
{
	cleanCampaigns($key, $codesWebsite);
}

function cleanCampaigns($websiteId, $codeStore)
{
	global $resource;
	global $writeConnection;
	/**
	 * Remove todas as regras deste website
	 */
	$removeRegraPromocao = "DELETE from ";
	$removeRegraPromocao .= $resource->getTableName('salesrule');
	$removeRegraPromocao .= " WHERE name LIKE '" . ($codeStore . "_%") . "' or name LIKE '" . ($codeStore . "-%") . "'";

	$writeConnection->query($removeRegraPromocao);
}