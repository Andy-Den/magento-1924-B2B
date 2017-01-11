<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/1/15
 * Time: 3:52 PM
 */
class FVets_ImportPromotions_Helper_Data extends Mage_Core_Helper_Data
{
	public function getLayoutModelUrl($entityType, $layoutVersion)
	{
		return str_replace('index.php/', '', Mage::getBaseUrl()) . 'media/fvets/importer/layouts/import_' . $entityType . '_model' . ($layoutVersion ? ('_v' . $layoutVersion) : '') . '.csv';
	}

	public function getManualUrl($entityType, $layoutVersion)
	{
		return str_replace('index.php/', '', Mage::getBaseUrl()) . 'media/fvets/importer/layouts/import_' . $entityType . '_manual' . ($layoutVersion ? ('_v' . $layoutVersion) : '') . '.pdf';
	}

	public function isUserAdministrator()
	{
		$adminUser = Mage::app()->getStore()->isAdmin();
		if ($adminUser)
		{
			$isUserAdministrator = Mage::getSingleton('admin/session')->getUser()->getRole()->getId() == 1;
			return $isUserAdministrator;
		}
		return false;
	}

	public function getActionXmlVars($actionName, $profile)
	{
		$xml = '<convert version="1.0"><profile name="default">' . $profile->getActionsXml()
			. '</profile></convert>';

		$convert = Mage::getModel('core/convert')
			->importXml($xml);

		$newProfile = $convert->importProfileXml('default');
		$container = $newProfile->getContainer($actionName);
		if ($container)
		{
			return $container->getVars();
		}
		return null;
	}
}